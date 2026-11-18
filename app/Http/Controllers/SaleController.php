<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoidSaleRequest;
use App\Models\Sale;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'sort' => ['nullable', 'in:transaction_code,total_amount,completed_at'],
        ]);
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->toDateString();
        if ($startDate > $endDate) {
            throw ValidationException::withMessages(['end_date' => 'End date must be on or after the start date.']);
        }
        $search = trim($filters['search'] ?? '');
        $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';
        $sales = Sale::query()->with(['customer', 'soldBy'])
            ->when(! $request->user()->hasPermission('SalesViewAll'), fn ($query) => $query->where('sold_by', $request->user()->id))
            ->whereDate('completed_at', '>=', $startDate)
            ->whereDate('completed_at', '<=', $endDate)
            ->when($search !== '', fn ($query) => $query->where(fn ($match) => $match
                ->where('transaction_code', 'like', $term)
                ->orWhereHas('customer', fn ($customer) => $customer->where('full_name', 'like', $term))))
            ->orderByDesc($filters['sort'] ?? 'completed_at')
            ->paginate(25)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function show(Sale $sale): View
    {
        $this->authorizeSale($sale);

        return view('sales.show', ['sale' => $sale->load(['items', 'customer', 'soldBy'])]);
    }

    public function export(Request $request, ReportController $reports, AuditLogger $audit): Response
    {
        // Keep sales exports consistent with the report filters and CSV escaping
        $request->merge(['type' => 'sales']);

        return $reports->export($request, $audit);
    }

    public function receipt(Sale $sale): View
    {
        $this->authorizeSale($sale);

        return view('sales.receipt', ['sale' => $sale->load(['items', 'customer', 'soldBy'])]);
    }

    public function void(VoidSaleRequest $request, Sale $sale, AuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($request, $sale, $audit): void {
            // Lock the sale so stock is restored only once
            $sale = Sale::query()->with('items')->whereKey($sale->id)->lockForUpdate()->firstOrFail();
            abort_if($sale->status === 'voided', 422, 'This sale has already been voided.');
            foreach ($sale->items as $item) {
                $product = $item->product()->withTrashed()->lockForUpdate()->firstOrFail();
                $product->increment('stock_quantity', $item->quantity);
                $product->refresh();
                $product->stockMovements()->create([
                    'sale_item_id' => $item->id,
                    'recorded_by_user_id' => auth()->id(),
                    'movement_type' => 'return',
                    'quantity_change' => $item->quantity,
                    'resulting_stock' => $product->stock_quantity,
                    'notes' => 'Void '.$sale->transaction_code,
                ]);
            }
            $sale->update(['status' => 'voided', 'void_reason' => $request->validated('reason')]);
            $audit->record('sale_voided', $sale, ['reason' => $request->validated('reason')]);
        });

        return back()->with('status', 'Sale voided.');
    }

    private function authorizeSale(Sale $sale): void
    {
        abort_unless(auth()->user()->hasPermission('SalesViewAll') || $sale->sold_by === auth()->id(), 404);
    }
}
