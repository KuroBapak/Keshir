<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Table;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

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

        // Show open bills + paid bills that still have items being cooked
        $openBills = Transaction::with(['details.product', 'details.variant', 'table'])
            ->where('cashier_id', auth()->id())
            ->where(function ($q) {
                $q->where('payment_status', 'open')
                  ->orWhere(function ($q2) {
                      $q2->where('payment_status', 'paid')
                         ->whereHas('details', fn($d) => $d->whereIn('status', ['pending', 'in_progress']));
                  });
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
            'order_type' => 'required|in:dine_in,takeaway',
        ]);

        $bill = $this->transactionService->createOpenBill(
            $request->only(['table_id', 'order_type']),
            auth()->id()
        );

        return redirect()->route('pos.bill', $bill)->with('success', 'Open Bill #' . $bill->id . ' berhasil dibuat.');
    }

    /**
     * View a specific open bill (add items interface).
     */
    public function showBill(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

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
        $this->authorizeAccess($transaction);

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
        $this->authorizeAccess($transaction);
        $detail = $transaction->details()->findOrFail($detailId);
        $this->transactionService->removeItemFromBill($transaction, $detail);
        return back()->with('success', 'Item dihapus.');
    }

    /**
     * Void entire bill.
     */
    public function voidBill(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);
        $this->transactionService->voidBill($transaction);
        return redirect()->route('pos.index')->with('success', 'Bill #' . $transaction->id . ' telah divoid.');
    }

    /**
     * Checkout / Payment.
     */
    public function checkout(Request $request, Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

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

        return redirect()->route('pos.receipt', $result)->with('success', 'Pembayaran berhasil!');
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

    private function authorizeAccess(Transaction $transaction): void
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
