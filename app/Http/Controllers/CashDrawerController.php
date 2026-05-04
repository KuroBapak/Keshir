<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\CashDrawerLog;
use Illuminate\Http\Request;

class CashDrawerController extends Controller
{
    /**
     * Show cash drawer management page (shift view).
     */
    public function index()
    {
        $activeDrawer = CashDrawer::where('status', 'open')
            ->first();

        $history = CashDrawer::where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->paginate(10);

        return view('dashboard.cash-drawer.index', compact('activeDrawer', 'history'));
    }

    /**
     * Open a new shift (cash drawer).
     */
    public function open(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        // Prevent double open globally
        $existing = CashDrawer::where('status', 'open')
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki shift aktif.');
        }

        CashDrawer::create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return back()->with('success', 'Shift dibuka. Modal awal: Rp ' . number_format($request->starting_cash, 0, ',', '.'));
    }

    /**
     * Show active shift detail with logs.
     */
    public function show(CashDrawer $cashDrawer)
    {
        $this->authorizeDrawer($cashDrawer);

        $logs = $cashDrawer->logs()->with('transaction')->orderBy('created_at', 'desc')->get();
        $totalIn = $logs->where('type', 'in')->sum('amount');
        $totalOut = $logs->where('type', 'out')->sum('amount');
        $expectedCash = $cashDrawer->starting_cash + $totalIn - $totalOut;

        return view('dashboard.cash-drawer.show', compact('cashDrawer', 'logs', 'totalIn', 'totalOut', 'expectedCash'));
    }

    /**
     * Close shift — cashier inputs physical ending cash.
     */
    public function close(Request $request, CashDrawer $cashDrawer)
    {
        $this->authorizeDrawer($cashDrawer);

        $request->validate([
            'ending_cash' => 'required|numeric|min:0',
        ]);

        $logs = $cashDrawer->logs;
        $totalIn = $logs->where('type', 'in')->sum('amount');
        $totalOut = $logs->where('type', 'out')->sum('amount');
        $expectedCash = $cashDrawer->starting_cash + $totalIn - $totalOut;

        $cashDrawer->update([
            'closed_at' => now(),
            'ending_cash' => $request->ending_cash,
            'expected_ending_cash' => $expectedCash,
            'status' => 'closed',
        ]);

        $diff = $request->ending_cash - $expectedCash;
        $message = 'Shift ditutup. ';
        $message .= $diff == 0 ? 'Kas sesuai ✅' : 'Selisih: Rp ' . number_format(abs($diff), 0, ',', '.') . ($diff > 0 ? ' (lebih)' : ' (kurang)');

        return redirect()->route('cash-drawer.index')->with('success', $message);
    }

    private function authorizeDrawer(CashDrawer $cashDrawer): void
    {
        // Drawer is store-wide, anyone can manage the active drawer
    }

    /**
     * Show detailed Sales Log for the currently OPEN shift.
     * Accessible by the cashier via the sidebar link.
     */
    public function shiftSales()
    {
        $activeDrawer = CashDrawer::where('status', 'open')
            ->first();

        // If no active shift, send empty collection to view
        $transactions = collect();
        if ($activeDrawer) {
            $transactions = \App\Models\Transaction::with(['details.product', 'details.variant', 'payment'])
                ->where('cash_drawer_id', $activeDrawer->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard.pos.shift-sales', compact('activeDrawer', 'transactions'));
    }
}
