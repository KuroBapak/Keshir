<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Daily sales summary report.
     */
    public function dailySummary(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $transactions = Transaction::with(['details.product', 'cashier', 'payment'])
            ->whereDate('created_at', $date)
            ->whereIn('payment_status', ['paid', 'void'])
            ->orderBy('created_at', 'desc')
            ->get();

        $paid = $transactions->where('payment_status', 'paid');
        $voided = $transactions->where('payment_status', 'void');

        $stats = [
            'total_transactions' => $paid->count(),
            'total_revenue' => $paid->sum('grand_total'),
            'total_subtotal' => $paid->sum('subtotal'),
            'total_tax' => $paid->sum('tax_total'),
            'total_service' => $paid->sum('service_total'),
            'total_discount' => $paid->sum('discount_total'),
            'cash_revenue' => $paid->where('payment_method', 'cash')->sum('grand_total'),
            'digital_revenue' => $paid->where('payment_method', 'digital')->sum('grand_total'),
            'voided_count' => $voided->count(),
        ];

        return view('dashboard.reports.daily', compact('date', 'transactions', 'stats'));
    }

    /**
     * Best-selling products report.
     */
    public function bestSelling(Request $request)
    {
        $period = $request->get('period', 'today');

        $query = TransactionDetail::select(
                'product_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(price * qty) as total_revenue')
            )
            ->whereHas('transaction', fn($q) => $q->where('payment_status', 'paid'));

        // Apply date filter
        match ($period) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'week' => $query->where('created_at', '>=', now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', now()->startOfMonth()),
            'all' => null, // no filter
        };

        $products = $query->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(20)
            ->get();

        return view('dashboard.reports.best-selling', compact('products', 'period'));
    }
}
