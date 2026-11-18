<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Services\AuditLogger;
use App\Services\ReportWorkbookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'payment' => ['nullable', 'in:cash,gcash'],
        ]);
        [$start, $end] = $this->reportDateRange($filters);
        $payment = $filters['payment'] ?? null;
        // Voided sales are excluded from report totals
        $sales = Sale::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->when($payment, fn ($query) => $query->where('payment_method', $payment));
        $salesTotal = (float) (clone $sales)->sum('total_amount');
        $transactionCount = (clone $sales)->count();
        $days = (int) $start->diffInDays($end->copy()->startOfDay()) + 1;
        $previous = Sale::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start->copy()->subDays($days), $start->copy()->subSecond()])
            ->when($payment, fn ($query) => $query->where('payment_method', $payment))
            ->sum('total_amount');
        $items = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.completed_at', [$start, $end])
            ->when($payment, fn ($query) => $query->where('sales.payment_method', $payment));

        return view('reports.index', [
            'salesTotal' => $salesTotal,
            'transactionCount' => $transactionCount,
            'averageSale' => $transactionCount ? $salesTotal / $transactionCount : 0,
            'growth' => $previous > 0 ? ($salesTotal - $previous) / $previous * 100 : 0,
            'startDate' => $start->toDateString(), 'endDate' => $end->toDateString(), 'payment' => $payment,
            'topProducts' => $items
                ->select('sale_items.product_id', 'sale_items.product_name')
                ->selectRaw('SUM(sale_items.quantity) AS units, SUM(sale_items.total_price) AS revenue')
                ->groupBy('sale_items.product_id', 'sale_items.product_name')
                ->orderByDesc('units')
                ->limit(5)
                ->get(),
            'paymentTotals' => (clone $sales)
                ->select('payment_method')
                ->selectRaw('SUM(total_amount) AS total')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method'),
            'dailyTotals' => (clone $sales)
                ->selectRaw('DATE(completed_at) AS sale_date, SUM(total_amount) AS total')
                ->groupBy(DB::raw('DATE(completed_at)'))
                ->orderBy('sale_date')
                ->get(),
        ]);
    }

    public function inventory(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'stock' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
        ]);
        $query = Product::query()->where('products.is_active', true)->with('category')
            ->when(! empty($filters['category']), fn ($products) => $products->where('category_id', $filters['category']))
            ->when(($filters['stock'] ?? null) === 'out_of_stock', fn ($products) => $products->where('stock_quantity', 0))
            ->when(($filters['stock'] ?? null) === 'low_stock', fn ($products) => $products
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold'))
            ->when(($filters['stock'] ?? null) === 'in_stock', fn ($products) => $products
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '>', 'low_stock_threshold'));

        return view('reports.inventory', [
            'products' => (clone $query)->orderBy('name')->paginate(50)->withQueryString(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'totalProducts' => (clone $query)->count(),
            'totalStock' => (clone $query)->sum('stock_quantity'),
            'lowStock' => (clone $query)->where('stock_quantity', '>', 0)->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'outOfStock' => (clone $query)->where('stock_quantity', 0)->count(),
            'categorySummary' => (clone $query)->join('categories', 'categories.id', '=', 'products.category_id')
                ->select('categories.name')->selectRaw('SUM(products.stock_quantity) AS stock_units')
                ->groupBy('categories.id', 'categories.name')->orderBy('categories.name')->get(),
        ]);
    }

    public function export(Request $request, AuditLogger $audit): Response
    {
        $filters = $request->validate([
            'type' => ['required', 'in:sales,inventory'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'payment' => ['nullable', 'in:cash,gcash'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'stock' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
        ]);
        $type = $filters['type'];
        if ($type === 'sales') {
            [$start, $end] = $this->reportDateRange($filters);
            $details = 'Period: '.$start->toDateString().' to '.$end->toDateString()
                .' | Payment: '.($filters['payment'] ?? 'all');
            $rows = Sale::query()->with('soldBy')->where('status', 'completed')->whereBetween('completed_at', [$start, $end])
                ->when(! empty($filters['payment']), fn ($query) => $query->where('payment_method', $filters['payment']))->latest('completed_at')->get();
        } else {
            $category = ! empty($filters['category']) ? Category::query()->find($filters['category'])?->name : 'All categories';
            $details = 'Category: '.$category.' | Stock: '.str_replace('_', ' ', $filters['stock'] ?? 'all');
            $rows = Product::query()->where('products.is_active', true)->with('category')
                ->when(! empty($filters['category']), fn ($query) => $query->where('category_id', $filters['category']))
                ->when(($filters['stock'] ?? null) === 'out_of_stock', fn ($query) => $query->where('stock_quantity', 0))
                ->when(($filters['stock'] ?? null) === 'low_stock', fn ($query) => $query
                    ->where('stock_quantity', '>', 0)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold'))
                ->when(($filters['stock'] ?? null) === 'in_stock', fn ($query) => $query
                    ->where('stock_quantity', '>', 0)
                    ->whereColumn('stock_quantity', '>', 'low_stock_threshold'))
                ->orderBy('name')->get();
        }
        $lines = [];
        if ($type === 'sales') {
            $headings = ['Transaction Code', 'Date', 'Cashier', 'Total', 'Payment'];
            foreach ($rows as $sale) {
                $lines[] = [
                    $sale->transaction_code,
                    optional($sale->completed_at)->toIso8601String(),
                    $sale->soldBy?->full_name,
                    (float) $sale->total_amount,
                    $sale->payment_method,
                ];
            }
        } else {
            $headings = ['Product', 'Category', 'Price', 'Stock', 'Low Stock Threshold'];
            foreach ($rows as $product) {
                $lines[] = [$product->name, $product->category?->name, (float) $product->price, (int) $product->stock_quantity, (int) $product->low_stock_threshold];
            }
        }
        $workbook = app(ReportWorkbookService::class)->create($type, $details, $headings, $lines);
        $audit->record('export', null, ['type' => $type]);

        return response($workbook, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$type.'-report.xlsx"',
        ]);
    }

    private function reportDateRange(array $filters): array
    {
        $end = Carbon::parse($filters['end_date'] ?? now()->toDateString())->endOfDay();
        $start = Carbon::parse($filters['start_date'] ?? $end->copy()->subDays(29)->toDateString())->startOfDay();
        if ($start->gt($end)) {
            throw ValidationException::withMessages(['end_date' => 'End date must be on or after the start date.']);
        }
        if ($start->diffInDays($end->copy()->startOfDay()) >= 30) {
            throw ValidationException::withMessages(['end_date' => 'The report date range cannot exceed 30 days.']);
        }

        return [$start, $end];
    }
}
