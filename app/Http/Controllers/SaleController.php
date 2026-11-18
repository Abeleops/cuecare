<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoidSaleRequest;
use App\Models\Sale;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->input('sort', 'completed_at');
        abort_unless(in_array($sort, ['transaction_code', 'total_amount', 'completed_at'], true), 422);
        $sales = Sale::query()->with(['customer', 'soldBy'])->when(! $request->user()->hasPermission('SalesViewAll'), fn ($query) => $query->where('sold_by', $request->user()->id))->when($request->filled('search'), fn ($query) => $query->where('transaction_code', 'like', '%'.$request->string('search').'%'))->orderByDesc($sort)->paginate(25)->withQueryString();

        return view('sales.index', ['sales' => $sales]);
    }

    public function show(Sale $sale): View
    {
        $this->authorizeSale($sale);

        return view('sales.show', ['sale' => $sale->load(['items', 'customer', 'soldBy'])]);
    }

    public function receipt(Sale $sale): View
    {
        $this->authorizeSale($sale);

        return view('sales.receipt', ['sale' => $sale->load(['items', 'customer', 'soldBy'])]);
    }

    public function void(VoidSaleRequest $request, Sale $sale, AuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($request, $sale, $audit): void {
            $sale = Sale::query()->with('items')->whereKey($sale->id)->lockForUpdate()->firstOrFail();
            abort_if($sale->status === 'voided', 422, 'This sale has already been voided.');
            foreach ($sale->items as $item) {
                $product = $item->product()->withTrashed()->lockForUpdate()->firstOrFail();
                $product->increment('stock_quantity', $item->quantity);
                $product->refresh();
                $product->stockMovements()->create(['sale_item_id' => $item->id, 'recorded_by_user_id' => auth()->id(), 'movement_type' => 'return', 'quantity_change' => $item->quantity, 'resulting_stock' => $product->stock_quantity, 'notes' => 'Void '.$sale->transaction_code]);
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
