<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\CartService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    private CartService $cartService;
    private TransactionService $transactionService;

    public function __construct(CartService $cartService, TransactionService $transactionService)
    {
        $this->cartService = $cartService;
        $this->transactionService = $transactionService;
        $this->setupMidtrans();
    }

    private function setupMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function process(Request $request)
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            return redirect()->route('public.menu')->with('error', 'Keranjang belanja kosong.');
        }

        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,booking',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'table_id' => 'required_if:order_type,dine_in,booking|nullable|exists:tables,id',
            'people_count' => 'required_if:order_type,dine_in,booking|nullable|integer|min:1',
            'booking_time' => 'required_if:order_type,booking|nullable|date|after:now',
        ]);

        // Validate table availability if not takeaway
        if ($request->order_type !== 'takeaway' && $request->table_id) {
            $table = \App\Models\Table::find($request->table_id);
            if ($table->status !== 'available') {
                return back()->with('error', 'Maaf, meja tersebut tidak tersedia saat ini.');
            }
        }

        // Calculate Totals
        // Settings for Tax & Service
        $settings = \App\Models\Setting::pluck('value', 'key');
        $taxRate = filter_var($settings['tax_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN) ? (float) ($settings['tax_rate'] ?? 0) : 0;
        $serviceRate = filter_var($settings['service_charge_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN) ? (float) ($settings['service_charge_rate'] ?? 0) : 0;

        $summary = $this->cartService->getSummary();
        $subtotal = $summary['subtotal'];
        $taxTotal = ($subtotal * $taxRate) / 100;
        $serviceTotal = ($subtotal * $serviceRate) / 100;
        $grandTotal = $subtotal + $taxTotal + $serviceTotal;

        // Build items payload for TransactionService (similar to POS)
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'addon_ids' => array_column($item['addons'], 'id'),
                'qty' => $item['qty'],
                'notes' => $item['notes'],
            ];
        }

        // Create transaction using service
        // Since it's from QR, it's open and source is qr
        $transaction = \DB::transaction(function () use ($request, $subtotal, $taxTotal, $serviceTotal, $grandTotal, $items) {
            $tx = Transaction::create([
                'order_type' => $request->order_type === 'takeaway' ? 'take_away' : $request->order_type,
                'source' => 'qr',
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'table_id' => $request->table_id,
                'people_count' => $request->people_count,
                'booking_time' => $request->booking_time,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'tax_total' => $taxTotal,
                'service_total' => $serviceTotal,
                'grand_total' => $grandTotal,
                'payment_status' => 'open', // Unpaid until Midtrans confirms
            ]);

            foreach ($items as $item) {
                $this->transactionService->addItemToBill($tx, $item);
            }

            if ($request->order_type === 'booking') {
                Booking::create([
                    'transaction_id' => $tx->id,
                    'booking_time' => $request->booking_time,
                    'status' => 'pending',
                ]);
            } else if ($request->order_type === 'dine_in' && $request->table_id) {
                // Reserve table immediately for dine-in
                $tx->table->update(['status' => 'occupied']);
            }

            return $tx;
        });

        // Request Midtrans Snap Token
        $midtransParams = [
            'transaction_details' => [
                'order_id' => 'QR-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'phone' => $request->phone,
            ],
            // Item details could be added here for detailed Midtrans receipt
        ];

        try {
            $snapToken = Snap::getSnapToken($midtransParams);
            
            // Log payment attempt
            Payment::create([
                'transaction_id' => $transaction->id,
                'method' => 'digital',
                'status' => 'pending',
                'midtrans_reference' => $midtransParams['transaction_details']['order_id'],
            ]);

            // Clear Cart after successful checkout intent
            $this->cartService->clearCart();

            return view('public.payment', compact('snapToken', 'transaction'));

        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            // If Midtrans fails, rollback transaction by voiding it
            $this->transactionService->voidBill($transaction);
            return back()->with('error', 'Gagal memproses pembayaran ke server Midtrans.');
        }
    }

    public function orderStatus(Transaction $transaction)
    {
        // Must be QR order
        if ($transaction->source !== 'qr') {
            abort(404);
        }

        $transaction->load(['details.product', 'details.variant', 'table']);

        return view('public.order-status', compact('transaction'));
    }
}
