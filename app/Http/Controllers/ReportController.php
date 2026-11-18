<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', ['salesTotal' => Sale::query()->where('status', 'completed')->sum('total_amount'), 'transactionCount' => Sale::query()->where('status', 'completed')->count(), 'lowStockCount' => Product::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count()]);
    }

    public function inventory(): View
    {
        return view('reports.inventory', ['products' => Product::query()->with('category')->orderBy('name')->paginate(50)]);
    }

    public function export(Request $request, AuditLogger $audit): Response
    {
        $type = $request->input('type', 'sales');
        abort_unless(in_array($type, ['sales', 'inventory'], true), 422);
        $rows = $type === 'sales' ? Sale::query()->with('soldBy')->where('status', 'completed')->latest('completed_at')->get() : Product::query()->with('category')->orderBy('name')->get();
        $lines = [];
        if ($type === 'sales') {
            $lines[] = ['Transaction Code', 'Date', 'Cashier', 'Total', 'Payment'];
            foreach ($rows as $sale) {
                $lines[] = [$sale->transaction_code, optional($sale->completed_at)->toIso8601String(), $sale->soldBy?->full_name, $sale->total_amount, $sale->payment_method];
            }
        } else {
            $lines[] = ['Product', 'Category', 'Price', 'Stock', 'Low Stock Threshold'];
            foreach ($rows as $product) {
                $lines[] = [$product->name, $product->category?->name, $product->price, $product->stock_quantity, $product->low_stock_threshold];
            }
        }
        $csv = collect($lines)->map(fn (array $line) => collect($line)->map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"')->implode(','))->implode("\r\n")."\r\n";
        $audit->record('export', null, ['type' => $type]);

        return response($csv, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$type.'-report.csv"']);
    }
}
