<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\Table;
use App\Models\Ingredient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isOwner = $user->role->name === 'owner';

        $productCount = Product::count();
        $categoryCount = Category::count();
        $tableCount = Table::where('status', 'available')->count();
        $ingredientCount = Ingredient::count();

        $today = Carbon::today();
        $todaySales = Transaction::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('grand_total');
        $todayOrders = Transaction::whereDate('created_at', $today)->count();
        $todayAttendance = AttendanceLog::whereDate('date', $today)->whereNotNull('check_in')->count();

        // Monthly Revenue & Growth
        $currentMonthSales = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('payment_status', 'paid')->sum('grand_total');
        $lastMonthSales = Transaction::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->where('payment_status', 'paid')->sum('grand_total');
        $revenueGrowth = $lastMonthSales > 0 ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : ($currentMonthSales > 0 ? 100 : 0);
        $currentMonthOrdersCount = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('payment_status', 'paid')->count();
        $averageOrderValue = $currentMonthOrdersCount > 0 ? round($currentMonthSales / $currentMonthOrdersCount) : 0;

        // Daily Revenue (7 days)
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $dailyRevRaw = Transaction::where('payment_status', 'paid')->whereDate('created_at', '>=', $sevenDaysAgo)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $dailyOrdRaw = Transaction::whereDate('created_at', '>=', $sevenDaysAgo)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $dailyRevenueLabels = []; $dailyRevenueData = []; $dailyOrdersData = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $sevenDaysAgo->copy()->addDays($i);
            $ds = $d->format('Y-m-d');
            $dailyRevenueLabels[] = $d->translatedFormat('d M');
            $dailyRevenueData[] = isset($dailyRevRaw[$ds]) ? (int) $dailyRevRaw[$ds]->total : 0;
            $dailyOrdersData[] = isset($dailyOrdRaw[$ds]) ? (int) $dailyOrdRaw[$ds]->total : 0;
        }

        // Monthly Revenue & Orders (6 months)
        $monthlyRevenueLabels = []; $monthlyRevenueData = []; $monthlyOrdersData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyRevenueLabels[] = $m->translatedFormat('M Y');
            $monthlyRevenueData[] = (int) Transaction::where('payment_status', 'paid')->whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->sum('grand_total');
            $monthlyOrdersData[] = (int) Transaction::whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
        }

        // Best Selling Products
        $bestSellingProducts = \App\Models\TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'paid')->whereMonth('transactions.created_at', now()->month)->whereYear('transactions.created_at', now()->year)
            ->select('transaction_details.product_id', DB::raw('SUM(transaction_details.qty) as total_sold'))
            ->groupBy('transaction_details.product_id')->orderByDesc('total_sold')->take(5)->get()
            ->map(function ($item) { $item->product = Product::find($item->product_id); return $item; });

        // Sales by Category
        $catRaw = \App\Models\TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('transactions.payment_status', 'paid')->whereMonth('transactions.created_at', now()->month)->whereYear('transactions.created_at', now()->year)
            ->select('categories.name', DB::raw('SUM(transaction_details.qty) as total_qty'))->groupBy('categories.id', 'categories.name')->get();
        $categoryChartLabels = $catRaw->pluck('name')->toArray();
        $categoryChartData = $catRaw->pluck('total_qty')->map(fn($v) => (int) $v)->toArray();

        // Order Type Distribution
        $otRaw = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->select('order_type', DB::raw('COUNT(*) as total'))->groupBy('order_type')->get()->keyBy('order_type');
        $orderTypeLabels = ['Dine In', 'Take Away', 'Booking'];
        $orderTypeData = [
            $otRaw->has('dine_in') ? (int) $otRaw['dine_in']->total : 0,
            $otRaw->has('take_away') ? (int) $otRaw['take_away']->total : 0,
            $otRaw->has('booking') ? (int) $otRaw['booking']->total : 0,
        ];

        // Staff Attendance Performance
        $staffPerformance = User::whereHas('role', fn($q) => $q->whereIn('name', ['cashier', 'kitchen_staff', 'manager']))
            ->with(['defaultShift'])->get()->map(function ($staff) {
                $logs = AttendanceLog::where('user_id', $staff->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->whereNotNull('check_in')->get();
                $hours = 0; $late = 0; $onTime = 0;
                foreach ($logs as $log) {
                    if ($log->check_out) $hours += Carbon::parse($log->check_in)->diffInMinutes(Carbon::parse($log->check_out)) / 60;
                    $log->status_in === 'late' ? $late++ : $onTime++;
                }
                return ['id' => $staff->id, 'name' => $staff->name, 'role' => $staff->role->name, 'shift' => $staff->defaultShift->name ?? '-',
                    'total_days' => $logs->count(), 'total_hours' => round($hours, 1), 'late_days' => $late, 'on_time_days' => $onTime];
            });

        // Monthly Work Hours (6 months)
        $monthlyWorkHoursLabels = []; $monthlyWorkHoursData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyWorkHoursLabels[] = $m->translatedFormat('M Y');
            $logs = AttendanceLog::whereMonth('date', $m->month)->whereYear('date', $m->year)->whereNotNull('check_in')->whereNotNull('check_out')->get();
            $h = 0;
            foreach ($logs as $log) $h += Carbon::parse($log->check_in)->diffInMinutes(Carbon::parse($log->check_out)) / 60;
            $monthlyWorkHoursData[] = round($h, 1);
        }
        $totalWorkHoursThisMonth = $monthlyWorkHoursData[5] ?? 0;

        return view('dashboard.index', compact(
            'isOwner', 'productCount', 'categoryCount', 'tableCount', 'ingredientCount',
            'todaySales', 'todayOrders', 'todayAttendance',
            'currentMonthSales', 'revenueGrowth', 'averageOrderValue', 'currentMonthOrdersCount',
            'dailyRevenueLabels', 'dailyRevenueData', 'dailyOrdersData',
            'monthlyRevenueLabels', 'monthlyRevenueData', 'monthlyOrdersData',
            'bestSellingProducts', 'categoryChartLabels', 'categoryChartData',
            'orderTypeLabels', 'orderTypeData',
            'staffPerformance', 'monthlyWorkHoursLabels', 'monthlyWorkHoursData', 'totalWorkHoursThisMonth'
        ));
    }
}
