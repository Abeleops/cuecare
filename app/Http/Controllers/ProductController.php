<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->input('sort', 'name');
        abort_unless(in_array($sort, ['name', 'price', 'stock_quantity', 'created_at'], true), 422);
        $products = Product::query()->with('category')->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))->orderBy($sort)->paginate(25)->withQueryString();
        return view('products.index', ['products' => $products, 'categories' => Category::query()->whereNull('parent_id')->with('children')->where('is_active', true)->get()]);
    }

    public function store(ProductRequest $request, AuditLogger $audit): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) { $data['image'] = $request->file('image')->store('products', 'public'); }
        $product = Product::query()->create($data);
        if ($product->stock_quantity > 0) { $product->stockMovements()->create(['recorded_by_user_id' => auth()->id(), 'movement_type' => 'restock', 'quantity_change' => $product->stock_quantity, 'resulting_stock' => $product->stock_quantity, 'notes' => 'Opening stock']); }
        $audit->record('product_created', $product);
        return back()->with('status', 'Product created.');
    }

    public function update(ProductRequest $request, Product $product, AuditLogger $audit): RedirectResponse
    {
        $oldPrice = $product->price;
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($product->image) { Storage::disk('public')->delete($product->image); }
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        unset($data['stock_quantity']);
        $product->update($data);
        if ((float) $oldPrice !== (float) $product->price) { $audit->record('product_price_changed', $product, ['old_price' => $oldPrice, 'new_price' => $product->price]); }
        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product, AuditLogger $audit): RedirectResponse
    {
        if ($product->image) { Storage::disk('public')->delete($product->image); }
        $product->delete();
        $audit->record('product_deleted', $product);
        return back()->with('status', 'Product deleted.');
    }

    public function adjustStock(StockAdjustmentRequest $request, Product $product, AuditLogger $audit): RedirectResponse
    {
        $change = (int) $request->validated('quantity_change');
        $result = $product->stock_quantity + $change;
        abort_if($result < 0, 422, 'Stock cannot be below zero.');
        $product->update(['stock_quantity' => $result]);
        $product->stockMovements()->create(['recorded_by_user_id' => auth()->id(), 'movement_type' => 'adjustment', 'quantity_change' => $change, 'resulting_stock' => $result, 'notes' => $request->validated('note')]);
        $audit->record('stock_adjustment', $product, ['quantity_change' => $change, 'resulting_stock' => $result]);
        return back()->with('status', 'Stock adjusted.');
    }
}
