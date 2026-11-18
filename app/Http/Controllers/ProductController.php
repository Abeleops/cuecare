<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\AuditLogger;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'group' => ['nullable', 'in:food,beverage,alcoholic'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'stock' => ['nullable', 'in:inStock,lowStock,outOfStock'],
            'sort' => ['nullable', 'in:name,price,stock_quantity,created_at'],
        ]);
        $search = trim($filters['search'] ?? '');
        $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
        $products = Product::query()->with('category')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$escapedSearch.'%'))
            ->when(! empty($filters['group']), fn ($query) => $query->whereHas(
                'category',
                fn ($category) => $category->where('category_group', $filters['group']),
            ))
            ->when(! empty($filters['category']), fn ($query) => $query->where('category_id', $filters['category']))
            ->when(($filters['stock'] ?? null) === 'outOfStock', fn ($query) => $query->where('stock_quantity', 0))
            ->when(($filters['stock'] ?? null) === 'lowStock', fn ($query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold'))
            ->when(($filters['stock'] ?? null) === 'inStock', fn ($query) => $query
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '>', 'low_stock_threshold'))
            ->orderBy($filters['sort'] ?? 'name')
            ->paginate(25)
            ->withQueryString();
        $groupCounts = [];
        foreach (['food', 'beverage', 'alcoholic'] as $group) {
            $groupCounts[$group] = Product::query()->whereHas('category', fn ($category) => $category->where('category_group', $group))->count();
        }

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->whereNull('parent_id')->with('children')->where('is_active', true)->get(),
            'availableCategories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'groupCounts' => $groupCounts,
            'allProductCount' => Product::query()->count(),
        ]);
    }

    public function store(ProductRequest $request, AuditLogger $audit, ImageUploadService $images): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $images->store($request->file('image'), 'products', 'image');
        }
        $product = Product::query()->create($data);
        if ($product->stock_quantity > 0) {
            $product->stockMovements()->create([
                'recorded_by_user_id' => auth()->id(),
                'movement_type' => 'restock',
                'quantity_change' => $product->stock_quantity,
                'resulting_stock' => $product->stock_quantity,
                'notes' => 'Opening stock',
            ]);
        }
        $audit->record('product_created', $product);

        return back()->with('status', 'Product created.');
    }

    public function update(ProductRequest $request, Product $product, AuditLogger $audit, ImageUploadService $images): RedirectResponse
    {
        $data = $request->validated();
        $stockChanged = array_key_exists('stock_quantity', $data)
            && (int) $data['stock_quantity'] !== (int) $data['original_stock'];
        if ($stockChanged) {
            abort_unless($request->user()->hasPermission('InventoryAdjust'), 403);
        }

        $previousImage = $product->image;
        $newImage = null;
        if ($request->hasFile('image')) {
            $newImage = $images->store($request->file('image'), 'products', 'image');
            $data['image'] = $newImage;
        } elseif ($request->boolean('remove_image')) {
            $data['image'] = null;
        }

        $newStock = $data['stock_quantity'] ?? null;
        $originalStock = $data['original_stock'] ?? null;
        unset($data['remove_image'], $data['stock_quantity'], $data['original_stock']);

        try {
            DB::transaction(function () use ($product, $data, $stockChanged, $newStock, $originalStock, $audit): void {
                // Lock the product before comparing the stock shown in the form
                $product = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
                if ($stockChanged && $product->stock_quantity !== (int) $originalStock) {
                    throw ValidationException::withMessages([
                        'stock_quantity' => 'Stock changed since you opened this form. Close and reopen it before updating stock.',
                    ])->errorBag('productEdit');
                }

                $oldPrice = $product->price;
                $product->update($data);
                if ($stockChanged) {
                    $change = (int) $newStock - $product->stock_quantity;
                    $product->update(['stock_quantity' => (int) $newStock]);
                    $product->stockMovements()->create([
                        'recorded_by_user_id' => auth()->id(),
                        'movement_type' => 'adjustment',
                        'quantity_change' => $change,
                        'resulting_stock' => (int) $newStock,
                        'notes' => 'Stock changed in product edit',
                    ]);
                    $audit->record('stock_adjustment', $product, ['quantity_change' => $change, 'resulting_stock' => (int) $newStock]);
                }
                if ((float) $oldPrice !== (float) $product->price) {
                    $audit->record('product_price_changed', $product, ['old_price' => $oldPrice, 'new_price' => $product->price]);
                }
            });
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            throw $exception;
        }

        if ($previousImage && ($newImage || $request->boolean('remove_image'))) {
            Storage::disk('public')->delete($previousImage);
        }

        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product, AuditLogger $audit): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        $audit->record('product_deleted', $product);

        return back()->with('status', 'Product deleted.');
    }

    public function adjustStock(StockAdjustmentRequest $request, Product $product, AuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($request, $product, $audit): void {
            // Lock the product while checking and recording the new stock
            $product = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
            $change = (int) $request->validated('quantity_change');
            $result = $product->stock_quantity + $change;
            abort_if($result < 0, 422, 'Stock cannot be below zero.');
            $product->update(['stock_quantity' => $result]);
            $product->stockMovements()->create([
                'recorded_by_user_id' => auth()->id(),
                'movement_type' => 'adjustment',
                'quantity_change' => $change,
                'resulting_stock' => $result,
                'notes' => $request->validated('note'),
            ]);
            $audit->record('stock_adjustment', $product, ['quantity_change' => $change, 'resulting_stock' => $result]);
        });

        return back()->with('status', 'Stock adjusted.');
    }
}
