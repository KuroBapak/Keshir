<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Create a new Open Bill transaction.
     */
    public function createOpenBill(array $data, int $cashierId): Transaction
    {
        return DB::transaction(function () use ($data, $cashierId) {
            // Find active shift for this cashier
            $activeDrawer = \App\Models\CashDrawer::where('user_id', $cashierId)
                ->where('status', 'open')
                ->first();

            // Calculate bill number within this shift
            $billNumber = 1;
            if ($activeDrawer) {
                $billNumber = Transaction::where('cash_drawer_id', $activeDrawer->id)->count() + 1;
            }

            $transaction = Transaction::create([
                'order_type' => $data['order_type'] ?? 'dine_in',
                'source' => 'pos',
                'customer_name' => !empty($data['customer_name']) ? $data['customer_name'] : ('Bill ' . $billNumber),
                'table_id' => $data['table_id'] ?? null,
                'cashier_id' => $cashierId,
                'cash_drawer_id' => $activeDrawer?->id,
                'bill_number' => $billNumber,
                'payment_status' => 'open',
                'subtotal' => 0,
                'discount_total' => 0,
                'tax_total' => 0,
                'service_total' => 0,
                'grand_total' => 0,
            ]);

            // Mark table as occupied
            if ($transaction->table_id) {
                $transaction->table()->update(['status' => 'occupied']);
            }

            return $transaction;
        });
    }

    /**
     * Add item(s) to an open bill.
     */
    public function addItemToBill(Transaction $transaction, array $item): TransactionDetail
    {
        return DB::transaction(function () use ($transaction, $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->base_price;

            // Add variant additional price
            if (!empty($item['product_variant_id'])) {
                $variant = $product->variants()->findOrFail($item['product_variant_id']);
                $price += $variant->additional_price;
            }

            $detail = $transaction->details()->create([
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'qty' => $item['qty'] ?? 1,
                'price' => $price,
                'notes' => $item['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Add add-ons
            if (!empty($item['addon_ids'])) {
                foreach ($item['addon_ids'] as $addonId) {
                    $addon = $product->addons()->findOrFail($addonId);
                    $detail->addons()->create([
                        'product_addon_id' => $addonId,
                        'price' => $addon->price,
                    ]);
                }
            }

            // Recalculate totals
            $this->recalculateTotals($transaction);

            return $detail;
        });
    }

    /**
     * Remove item from bill.
     */
    public function removeItemFromBill(Transaction $transaction, TransactionDetail $detail): void
    {
        DB::transaction(function () use ($transaction, $detail) {
            $detail->addons()->delete();
            $detail->delete();
            $this->recalculateTotals($transaction);
        });
    }

    /**
     * Void / Cancel an entire open bill.
     */
    public function voidBill(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // If items were already sent to kitchen (cooking/done), restock ingredients
            $sentDetails = $transaction->details()
                ->whereIn('status', ['in_progress', 'done'])
                ->get();

            foreach ($sentDetails as $detail) {
                $this->restockIngredients($detail);
            }

            // Release the table
            if ($transaction->table_id) {
                $transaction->table()->update(['status' => 'available']);
            }

            // Mark transaction as void
            $transaction->update(['payment_status' => 'void']);
        });
    }

    /**
     * Checkout: finalize and pay the bill.
     */
    public function checkout(Transaction $transaction, array $paymentData): Transaction
    {
        return DB::transaction(function () use ($transaction, $paymentData) {
            // Apply discount if provided
            if (!empty($paymentData['discount_id'])) {
                $transaction->update(['discount_id' => $paymentData['discount_id']]);
            }

            // Recalculate with possible discount
            $this->recalculateTotals($transaction->fresh());
            $transaction->refresh();

            // Deduct ingredients only for items not yet deducted (pending status)
            // POS orders: already deducted when kitchen marks in_progress
            // QR orders: deduct now at payment time
            foreach ($transaction->details as $detail) {
                if ($detail->status === 'pending') {
                    $this->deductIngredients($detail);
                }
            }

            // Create payment
            $method = $paymentData['method'] ?? 'cash';
            $payment = $transaction->payment()->create([
                'method' => $method,
                'status' => $method === 'cash' ? 'paid' : 'pending',
                'amount_paid' => $paymentData['amount_paid'] ?? $transaction->grand_total,
                'change_amount' => $method === 'cash'
                    ? max(0, ($paymentData['amount_paid'] ?? $transaction->grand_total) - $transaction->grand_total)
                    : 0,
            ]);

            // Update transaction status
            $transaction->update([
                'payment_status' => $method === 'cash' ? 'paid' : 'open',
                'payment_method' => $method,
            ]);

            // Auto-log cash in to active cash drawer
            if ($method === 'cash') {
                $activeDrawer = \App\Models\CashDrawer::where('user_id', $paymentData['cashier_id'] ?? $transaction->cashier_id)
                    ->where('status', 'open')
                    ->first();

                if ($activeDrawer) {
                    $activeDrawer->logs()->create([
                        'type' => 'in',
                        'amount' => $transaction->grand_total,
                        'description' => 'Penjualan Bill #' . $transaction->id,
                        'transaction_id' => $transaction->id,
                    ]);
                }
            }

            // Table remains occupied until cashier clears it manually from the Tables menu
            // if ($method === 'cash' && $transaction->table_id) {
            //     $transaction->table()->update(['status' => 'available']);
            // }

            return $transaction->fresh();
        });
    }

    /**
     * Recalculate subtotal, tax, service, discount, grand total.
     */
    public function recalculateTotals(Transaction $transaction): void
    {
        $details = $transaction->details()->with('addons')->get();

        $subtotal = 0;
        foreach ($details as $detail) {
            $itemTotal = $detail->price * $detail->qty;
            $addonTotal = $detail->addons->sum('price') * $detail->qty;
            $subtotal += $itemTotal + $addonTotal;
        }

        // Discount
        $discountTotal = 0;
        if ($transaction->discount_id) {
            $discount = $transaction->discount;
            if ($discount) {
                $discountTotal = $discount->type === 'percentage'
                    ? $subtotal * ($discount->value / 100)
                    : $discount->value;
                $discountTotal = min($discountTotal, $subtotal);
            }
        }

        $afterDiscount = $subtotal - $discountTotal;

        // Tax & Service
        $taxEnabled = Setting::getValue('tax_enabled', '0') === '1';
        $taxRate = $taxEnabled ? (float) Setting::getValue('tax_rate', '11') : 0;
        $serviceEnabled = Setting::getValue('service_charge_enabled', '0') === '1';
        $serviceRate = $serviceEnabled ? (float) Setting::getValue('service_charge_rate', '5') : 0;

        $taxTotal = $afterDiscount * ($taxRate / 100);
        $serviceTotal = $afterDiscount * ($serviceRate / 100);
        $grandTotal = $afterDiscount + $taxTotal + $serviceTotal;

        $transaction->update([
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'service_total' => round($serviceTotal, 2),
            'grand_total' => round($grandTotal, 2),
        ]);
    }

    /**
     * Deduct ingredients using FIFO (oldest expiry batch first).
     * Called by KitchenController when item moves to in_progress (POS),
     * or by checkout() for QR orders.
     */
    public function deductIngredients(TransactionDetail $detail): void
    {
        $product = $detail->product;
        $recipe = $product->recipe;
        if (!$recipe) return;

        foreach ($recipe->details as $recipeDetail) {
            $needed = $recipeDetail->quantity * $detail->qty;
            $batches = IngredientBatch::where('ingredient_id', $recipeDetail->ingredient_id)
                ->where('stock', '>', 0)
                ->orderBy('expiry_date', 'asc') // FIFO
                ->get();

            foreach ($batches as $batch) {
                if ($needed <= 0) break;
                $deduct = min($needed, $batch->stock);
                $batch->decrement('stock', $deduct);
                $needed -= $deduct;
            }

            // Update total stock
            $ingredient = Ingredient::find($recipeDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->update(['total_stock' => $ingredient->batches()->sum('stock')]);
            }
        }
    }

    /**
     * Restock ingredients (when voiding an order).
     */
    public function restockIngredients(TransactionDetail $detail): void
    {
        $product = $detail->product;
        $recipe = $product->recipe;
        if (!$recipe) return;

        foreach ($recipe->details as $recipeDetail) {
            $restockQty = $recipeDetail->quantity * $detail->qty;

            // Add back to the newest batch (or create a restock batch)
            $latestBatch = IngredientBatch::where('ingredient_id', $recipeDetail->ingredient_id)
                ->orderBy('expiry_date', 'desc')
                ->first();

            if ($latestBatch) {
                $latestBatch->increment('stock', $restockQty);
            }

            // Update total stock
            $ingredient = Ingredient::find($recipeDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->update(['total_stock' => $ingredient->batches()->sum('stock')]);
            }
        }
    }
}
