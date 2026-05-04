<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    /**
     * List all refund logs.
     */
    public function index()
    {
        $refunds = Refund::with(['transaction', 'authorizedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.refunds.index', compact('refunds'));
    }

    /**
     * Show refund form for a paid transaction.
     */
    public function create(Transaction $transaction)
    {
        if ($transaction->payment_status !== 'paid') {
            return back()->with('error', 'Hanya transaksi yang sudah dibayar yang bisa di-refund.');
        }

        return view('dashboard.refunds.create', compact('transaction'));
    }

    /**
     * Process refund: log refund, restock ingredients, void transaction.
     */
    public function store(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01|max:' . $transaction->grand_total,
        ]);

        if ($transaction->payment_status !== 'paid') {
            return back()->with('error', 'Transaksi ini tidak bisa di-refund.');
        }

        DB::transaction(function () use ($request, $transaction) {
            // Create refund log
            Refund::create([
                'transaction_id' => $transaction->id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'authorized_by' => auth()->id(),
            ]);

            // Restock ingredients for cooked items
            foreach ($transaction->details as $detail) {
                if (in_array($detail->status, ['in_progress', 'done'])) {
                    $this->transactionService->restockIngredients($detail);
                }
            }

            // Mark transaction as void
            $transaction->update(['payment_status' => 'void']);

            // Release table
            if ($transaction->table_id) {
                $transaction->table()->update(['status' => 'available']);
            }

            // Log cash out in active cash drawer (if cash payment)
            if ($transaction->payment_method === 'cash') {
                $activeDrawer = \App\Models\CashDrawer::where('status', 'open')->first();

                if ($activeDrawer) {
                    $activeDrawer->logs()->create([
                        'type' => 'out',
                        'amount' => $request->amount,
                        'description' => 'Refund #' . $transaction->id . ': ' . $request->reason,
                        'transaction_id' => $transaction->id,
                    ]);
                }
            }
        });

        return redirect()->route('refunds.index')->with('success', 'Refund berhasil dicatat. Stok bahan telah dikembalikan.');
    }
}
