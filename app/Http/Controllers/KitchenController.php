<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    /**
     * Kitchen dashboard — show active tickets.
     */
    public function index()
    {
        $tickets = Transaction::with(['details.product', 'details.variant', 'details.addons.addon', 'table'])
            ->where(function ($q) {
                // POS orders go to kitchen immediately (Open Bill)
                // QR orders MUST be paid first (FR-17)
                $q->where(function ($q2) {
                    $q2->where('source', 'pos')
                       ->whereIn('payment_status', ['open', 'paid']);
                })->orWhere(function ($q2) {
                    $q2->where('source', 'qr')
                       ->where('payment_status', 'paid');
                });
            })
            ->whereHas('details', fn($q) => $q->whereIn('status', ['pending', 'in_progress']))
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.index', compact('tickets'));
    }

    /**
     * Update item status (pending -> in_progress -> done).
     * FIFO deduction happens when status changes to in_progress.
     */
    public function updateStatus(Request $request, TransactionDetail $detail)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $newStatus = $request->status;
        $oldStatus = $detail->status;

        // Deduct ingredients when kitchen starts cooking (pending → in_progress)
        if ($oldStatus === 'pending' && $newStatus === 'in_progress') {
            $this->transactionService->deductIngredients($detail);
        }

        $detail->update(['status' => $newStatus]);

        return back()->with('success', 'Status item diupdate.');
    }

    /**
     * Mark all items of a transaction as done.
     */
    public function markAllDone(Transaction $transaction)
    {
        // Deduct ingredients for any pending items (not yet deducted)
        $pendingDetails = $transaction->details()->where('status', 'pending')->get();
        foreach ($pendingDetails as $detail) {
            $this->transactionService->deductIngredients($detail);
        }

        $transaction->details()
            ->whereIn('status', ['pending', 'in_progress'])
            ->update(['status' => 'done']);

        return back()->with('success', 'Semua item sudah selesai.');
    }
}
