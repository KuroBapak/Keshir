<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PosController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
        $this->setupMidtrans();
    }

    private function setupMidtrans()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY', config('midtrans.server_key'));
        $isProdEnv = env('MIDTRANS_IS_PRODUCTION', config('midtrans.is_production', false));
        Config::$isProduction = is_string($isProdEnv) ? filter_var($isProdEnv, FILTER_VALIDATE_BOOLEAN) : $isProdEnv;
        Config::$isSanitized = filter_var(env('MIDTRANS_IS_SANITIZED', config('midtrans.is_sanitized', true)), FILTER_VALIDATE_BOOLEAN);
        Config::$is3ds = filter_var(env('MIDTRANS_IS_3DS', config('midtrans.is_3ds', true)), FILTER_VALIDATE_BOOLEAN);

        if (!Config::$isProduction) {
            Config::$curlOptions = [
                \CURLOPT_SSL_VERIFYPEER => false,
                \CURLOPT_HTTPHEADER => ['X-Midtrans-Local: true']
            ];
        }
    }

    /**
     * Main POS page — catalog + cart + open bills.
     */
    public function index()
    {
        $categories = Category::has('products')->get();
        $products = Product::with(['variants', 'addons'])
            ->where('is_active', true)
            ->get();
        $tables = Table::where('status', 'available')->get();
        $discounts = Discount::where('is_active', true)->get();

        // Get active shift for this cashier to show all their bills in the current shift
        $activeShift = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        // Show ALL bills for the current shift (open, paid, void, refunded)
        // They will stack up until the shift is closed.
        $openBills = Transaction::with(['details.product', 'details.variant', 'table'])
            ->where('cashier_id', auth()->id())
            ->when($activeShift, function ($query) use ($activeShift) {
                return $query->where('created_at', '>=', $activeShift->opened_at);
            }, function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if cashier has active shift (owner/manager bypass)
        $isCashier = auth()->user()->role->name === 'cashier';
        $hasActiveShift = !$isCashier || CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        return view('pos.index', compact('categories', 'products', 'tables', 'discounts', 'openBills', 'hasActiveShift'));
    }

    /**
     * Create new open bill.
     */
    public function createBill(Request $request)
    {
        // Enforce shift opened (cashier only)
        if (auth()->user()->role->name === 'cashier') {
            $hasShift = CashDrawer::where('user_id', auth()->id())
                ->where('status', 'open')
                ->exists();

            if (!$hasShift) {
                return back()->with('error', '⚠️ Anda belum membuka shift. Silakan buka shift di Kas Laci terlebih dahulu.');
            }
        }

        $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'order_type' => 'required|in:dine_in,take_away',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['table_id', 'order_type', 'customer_name']);
        
        if ($data['order_type'] === 'take_away') {
            $data['table_id'] = null;
        }

        $bill = $this->transactionService->createOpenBill(
            $data,
            auth()->id()
        );

        return redirect()->route('pos.bill', $bill)->with('success', 'Open Bill #' . $bill->id . ' berhasil dibuat.');
    }

    /**
     * View a specific open bill (add items interface).
     */
    public function showBill(Transaction $transaction)
    {

        $transaction->load([
            'details.product', 'details.variant', 'details.addons.addon',
            'table', 'discount',
        ]);

        $categories = Category::has('products')->get();
        $products = Product::with(['variants', 'addons'])->where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)->get();

        return view('pos.bill', compact('transaction', 'categories', 'products', 'discounts'));
    }

    /**
     * Add item to bill.
     */
    public function addItem(Request $request, Transaction $transaction)
    {
        $this->authorizeEdit($transaction);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:product_addons,id',
        ]);

        $this->transactionService->addItemToBill($transaction, $request->only([
            'product_id', 'product_variant_id', 'qty', 'notes', 'addon_ids',
        ]));

        return back()->with('success', 'Item ditambahkan.');
    }

    /**
     * Remove item from bill.
     */
    public function removeItem(Transaction $transaction, $detailId)
    {
        $this->authorizeEdit($transaction);
        $detail = $transaction->details()->findOrFail($detailId);
        $this->transactionService->removeItemFromBill($transaction, $detail);
        return back()->with('success', 'Item dihapus.');
    }

    /**
     * Void entire bill.
     */
    public function voidBill(Transaction $transaction)
    {
        $this->authorizeEdit($transaction);
        $this->transactionService->voidBill($transaction);
        return redirect()->route('pos.index')->with('success', 'Bill #' . $transaction->id . ' telah divoid.');
    }

    /**
     * Checkout / Payment.
     */
    public function checkout(Request $request, Transaction $transaction)
    {
        $this->authorizeEdit($transaction);

        $request->validate([
            'method' => 'required|in:cash,digital',
            'amount_paid' => 'required_if:method,cash|numeric|min:0',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        // Cash validation
        if ($request->method === 'cash') {
            $this->transactionService->recalculateTotals($transaction->fresh());
            $transaction->refresh();
            if ($request->amount_paid < $transaction->grand_total) {
                return back()->with('error', 'Uang yang diterima kurang dari total tagihan.');
            }
        }

        $result = $this->transactionService->checkout($transaction, $request->only([
            'method', 'amount_paid', 'discount_id',
        ]));

        // For cash: direct to receipt
        if ($request->method === 'cash') {
            return redirect()->route('pos.receipt', $result)->with('success', 'Pembayaran berhasil!');
        }

        // For digital: redirect to POS payment page with Snap token
        return redirect()->route('pos.payment', $result);
    }

    /**
     * Show Midtrans payment page for a POS digital bill.
     */
    public function payment(Transaction $transaction)
    {
        $transaction->load(['details.product', 'details.variant', 'details.addons.addon', 'table', 'discount']);

        // Generate Midtrans Snap Token
        $this->transactionService->recalculateTotals($transaction->fresh());
        $transaction->refresh();

        $midtransParams = [
            'transaction_details' => [
                'order_id' => 'POS-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $transaction->grand_total,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name ?? 'Pelanggan POS',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($midtransParams);

            // Create or update payment record
            $payment = $transaction->payment;
            if ($payment) {
                $payment->update(['midtrans_reference' => $midtransParams['transaction_details']['order_id']]);
            } else {
                Payment::create([
                    'transaction_id' => $transaction->id,
                    'method' => 'digital',
                    'status' => 'pending',
                    'midtrans_reference' => $midtransParams['transaction_details']['order_id'],
                ]);
            }

            return view('pos.payment', compact('snapToken', 'transaction'));

        } catch (\Exception $e) {
            \Log::error('POS Midtrans Snap Error: ' . $e->getMessage());
            return redirect()->route('pos.bill', $transaction)
                ->with('error', 'Gagal memproses pembayaran digital: ' . $e->getMessage());
        }
    }

    /**
     * Confirm POS digital payment after Midtrans success callback.
     */
    public function confirmDigital(Request $request, Transaction $transaction)
    {
        $transaction->update(['payment_status' => 'paid']);

        if ($transaction->payment) {
            $transaction->payment->update([
                'status' => 'paid',
                'amount_paid' => $transaction->grand_total,
            ]);
        }

        // Table remains occupied until cashier clears it manually from the Tables menu
        // if ($transaction->table_id) {
        //     $transaction->table()->update(['status' => 'available']);
        // }

        // Auto-log to cash drawer if applicable
        $activeDrawer = CashDrawer::where('user_id', $transaction->cashier_id)
            ->where('status', 'open')
            ->first();
        if ($activeDrawer) {
            $activeDrawer->logs()->create([
                'type' => 'in',
                'amount' => $transaction->grand_total,
                'description' => 'Digital Payment Bill #' . $transaction->id,
                'transaction_id' => $transaction->id,
            ]);
        }

        return redirect()->route('pos.receipt', $transaction)->with('success', 'Pembayaran digital berhasil!');
    }

    /**
     * Receipt page.
     */
    public function receipt(Transaction $transaction)
    {
        $transaction->load([
            'details.product', 'details.variant', 'details.addons.addon',
            'payment', 'table', 'cashier', 'discount',
        ]);
        return view('pos.receipt', compact('transaction'));
    }

    private function authorizeEdit(Transaction $transaction): void
    {
        if ($transaction->payment_status !== 'open') {
            abort(403, 'Bill ini sudah tidak bisa diedit.');
        }
    }

    /**
     * View list of incoming bookings.
     */
    public function bookings()
    {
        $bookings = \App\Models\Booking::with(['transaction.table', 'transaction.details.product', 'transaction.details.variant', 'transaction.details.addons.addon'])
            ->orderByRaw("FIELD(status, 'pending', 'confirmed', 'completed', 'cancelled')")
            ->orderBy('booking_time', 'asc')
            ->get();

        return view('dashboard.pos.bookings', compact('bookings'));
    }

    /**
     * Update Booking status.
     * Table locking logic is applied here.
     */
    public function updateBookingStatus(Request $request, \App\Models\Booking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,completed,cancelled']);
        
        $oldStatus = $booking->status;
        $newStatus = $request->status;
        $transaction = $booking->transaction;
        $table = $transaction->table;

        $booking->update(['status' => $newStatus]);

        if ($table) {
            // If booking is confirmed or pending, table stays booked
            if (in_array($newStatus, ['pending', 'confirmed'])) {
                $table->update(['status' => 'booked']);
            } 
            // If completed or cancelled, table is freed
            else if (in_array($newStatus, ['completed', 'cancelled'])) {
                $table->update(['status' => 'available']);
                
                // If cancelled and it was unpaid QR order or open POS order, void the bill
                if ($newStatus === 'cancelled' && $transaction->payment_status === 'open') {
                    $this->transactionService->voidBill($transaction);
                }
            }
        }

        return back()->with('success', 'Status booking diperbarui.');
    }

    /**
     * View POS Table Grid for Dine-In Management.
     */
    public function tables()
    {
        // Get all tables ordered by number
        $tables = \App\Models\Table::orderBy('table_number')->get();
        // Load active transactions for occupied tables to show who is sitting there
        $activeDineIn = \App\Models\Transaction::where('order_type', 'dine_in')
            ->whereIn('payment_status', ['open', 'paid']) // Only show active ones
            ->get()
            ->keyBy('table_id');

        return view('dashboard.pos.tables', compact('tables', 'activeDineIn'));
    }

    /**
     * Cashier forcefully marks a table as available after customers leave.
     */
    public function clearTable(Request $request, \App\Models\Table $table)
    {
        if ($table->status === 'booked') {
            return back()->with('error', 'Meja ini sedang dibooking. Ubah status reservasi di menu Reservasi.');
        }

        $table->update(['status' => 'available']);
        return back()->with('success', "Meja {$table->table_number} berhasil dikosongkan.");
    }
}