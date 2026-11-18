<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $start = Carbon::parse($filters['start_date'] ?? today()->toDateString())->startOfDay();
        $end = Carbon::parse($filters['end_date'] ?? today()->toDateString())->endOfDay();
        if ($start->gt($end)) {
            throw ValidationException::withMessages(['end_date' => 'End date must be on or after the start date.']);
        }
        $days = (int) $start->diffInDays($end->copy()->startOfDay()) + 1;
        $completedSales = Sale::query()->where('status', 'completed');
        $todaySales = (clone $completedSales)->whereDate('completed_at', today());
        $todayTotal = (float) $todaySales->sum('total_amount');
        $todayTransactions = $todaySales->count();
        $periodSales = (clone $completedSales)->whereBetween('completed_at', [$start, $end]);
        $periodTotal = (float) (clone $periodSales)->sum('total_amount');
        $periodTransactions = (clone $periodSales)->count();
        // Compare the selected range with the preceding period of equal length
        $previousSales = (clone $completedSales)
            ->whereBetween('completed_at', [$start->copy()->subDays($days), $start->copy()->subSecond()]);
        $previousTotal = (float) $previousSales->sum('total_amount');
        $previousTransactions = $previousSales->count();
        $salesGrowth = $previousTotal > 0 ? (($periodTotal - $previousTotal) / $previousTotal) * 100 : 0;
        $transactionGrowth = $previousTransactions > 0 ? (($periodTransactions - $previousTransactions) / $previousTransactions) * 100 : 0;
        $periodItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.completed_at', [$start, $end]);
        $topProducts = (clone $periodItems)
            ->select('sale_items.product_id', 'sale_items.product_name')
            ->selectRaw('SUM(sale_items.quantity) AS units_sold, SUM(sale_items.total_price) AS revenue')
            ->groupBy('sale_items.product_id', 'sale_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();
        $categorySales = (clone $periodItems)
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.category_group')
            ->selectRaw('SUM(sale_items.total_price) AS revenue')
            ->groupBy('categories.category_group')
            ->get();

        return view('dashboard.index', [
            'todayTotal' => $todayTotal,
            'todayTransactions' => $todayTransactions,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'periodTotal' => $periodTotal,
            'periodTransactions' => $periodTransactions,
            'salesGrowth' => $salesGrowth,
            'transactionGrowth' => $transactionGrowth,
            'topProducts' => $topProducts,
            'categorySales' => $categorySales,
            'customerCount' => Customer::query()->count(),
            'averageOrderValue' => $periodTransactions > 0 ? $periodTotal / $periodTransactions : 0,
            'recentSales' => Sale::query()->latest('completed_at')->limit(5)->get(),
            'lowStockProducts' => Product::query()
                ->where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->orderBy('stock_quantity')
                ->limit(5)
                ->get(),
            'itemsSold' => DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->where('sales.status', 'completed')
                ->whereDate('sales.completed_at', today())
                ->sum('sale_items.quantity'),
        ]);
    }
}
