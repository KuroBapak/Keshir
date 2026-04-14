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
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', config('midtrans.server_key'));
        // Parse boolean properly from env string or bool
        $isProdEnv = env('MIDTRANS_IS_PRODUCTION', config('midtrans.is_production', false));
        Config::$isProduction = is_string($isProdEnv) ? filter_var($isProdEnv, FILTER_VALIDATE_BOOLEAN) : $isProdEnv;
        Config::$isSanitized = filter_var(env('MIDTRANS_IS_SANITIZED', config('midtrans.is_sanitized', true)), FILTER_VALIDATE_BOOLEAN);
        Config::$is3ds = filter_var(env('MIDTRANS_IS_3DS', config('midtrans.is_3ds', true)), FILTER_VALIDATE_BOOLEAN);
        
        // Disable SSL verifypeer on local/sandbox to fix curl error
        // We include a dummy header to avoid "Undefined array key 10023" bug in Midtrans PHP client.
        if (!Config::$isProduction) {
            Config::$curlOptions = [
                \CURLOPT_SSL_VERIFYPEER => false,
                \CURLOPT_HTTPHEADER => ['X-Midtrans-Local: true'] 
            ];
        }
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
                if ($tx->table_id) {
                    $tx->table->update(['status' => 'booked']);
                }
            } else if ($request->order_type === 'dine_in' && $request->table_id) {
                // Reserve table immediately for dine-in
                $tx->table->update(['status' => 'occupied']);
            }

            return $tx;
        });

        // Check if TUNAI
        if ($request->is_cash === 'true') {
            // For TUNAI we skip Midtrans, clear cart, and redirect to order status.
            // Cashier will see this bill as "open" and will handle payment manually.
            $this->cartService->clearCart();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'redirect_url' => route('public.order-status', $transaction)
                ]);
            }
            return redirect()->route('public.order-status', $transaction);
        }

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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'snap_token' => $snapToken,
                    'redirect_url' => route('public.order-status', $transaction)
                ]);
            }

            return view('public.payment', compact('snapToken', 'transaction'));

        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            // If Midtrans fails, rollback transaction by voiding it
            $this->transactionService->voidBill($transaction);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses pembayaran ke server Midtrans. Pastikan Server Key di .env benar. (Error: ' . $e->getMessage() . ')'
                ], 500);
            }
            
            return back()->with('error', 'Gagal memproses pembayaran ke server Midtrans.');
        }
    }

    public function orderStatus(Transaction $transaction)
    {
        // Must be QR order
        if ($transaction->source !== 'qr') {
            abort(404);
        }
        
        $transaction->load(['payment']);

        // Local environment auto-sync (if webhook hasn't arrived)
        if ($transaction->payment_status === 'open' && $transaction->payment && $transaction->payment->midtrans_reference) {
            try {
                $status = \Midtrans\Transaction::status($transaction->payment->midtrans_reference);
                if (is_object($status)) {
                    $transactionInfo = $status->transaction_status ?? '';
                    if ($transactionInfo === 'capture' || $transactionInfo === 'settlement') {
                        // Process Success
                        $transaction->payment->update(['status' => 'success', 'amount_paid' => (float)$status->gross_amount]);
                        $transaction->update(['payment_status' => 'paid']);
                        $transaction->load('details.addons');
                        foreach ($transaction->details as $detail) {
                            $this->transactionService->deductIngredients($detail);
                        }
                    } elseif (in_array($transactionInfo, ['deny', 'expire', 'cancel'])) {
                        // Process Failure
                        $transaction->payment->update(['status' => 'failed']);
                        $this->transactionService->voidBill($transaction);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Midtrans Status Sync Error: " . $e->getMessage());
            }
        }

        $transaction->refresh()->load(['details.product', 'details.variant', 'table']);

        return view('public.order-status', compact('transaction'));
    }
}
