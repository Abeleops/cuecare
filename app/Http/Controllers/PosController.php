<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function searchCustomers(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'max:100']]);
        $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($data['q']));
        $customers = Customer::query()
            ->where(function ($query) use ($term): void {
                $query->where('full_name', 'like', '%'.$term.'%')
                    ->orWhere('phone', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            })
            ->orderBy('full_name')
            ->limit(10)
            ->get(['id', 'full_name', 'phone'])
            ->map(fn (Customer $customer): array => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'phone' => $customer->phone,
            ]);

        return response()->json($customers);
    }

    public function cart(Request $request, CartService $cart): JsonResponse
    {
        return response()->json($cart->snapshot($request));
    }

    public function discount(Request $request, CartService $cart): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ]);
        if ($data['type'] === 'percentage' && (float) $data['value'] > 100) {
            throw ValidationException::withMessages(['value' => 'Percentage cannot exceed 100%.']);
        }
        $current = $cart->snapshot($request);
        if ($data['type'] === 'fixed' && (int) round((float) $data['value'] * 100) > $current['subtotal_cents']) {
            throw ValidationException::withMessages(['value' => 'Discount cannot exceed the subtotal.']);
        }
        $request->session()->put('pos.discount', $data);

        return response()->json($cart->snapshot($request));
    }

    public function updateCart(Request $request, Product $product, CartService $cart): JsonResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:1000']]);
        if (! $product->is_active || $data['quantity'] > $product->stock_quantity) {
            throw ValidationException::withMessages(['quantity' => 'Quantity exceeds available stock.']);
        }
        $items = $request->session()->get('pos.cart', []);
        $items[$product->id] = (int) $data['quantity'];
        $request->session()->put('pos.cart', $items);
        // Clear the discount when the cart changes
        $request->session()->forget('pos.discount');

        return response()->json($cart->snapshot($request));
    }

    public function removeCart(Request $request, Product $product, CartService $cart): JsonResponse
    {
        $items = $request->session()->get('pos.cart', []);
        unset($items[$product->id]);
        $request->session()->put('pos.cart', $items);
        $request->session()->forget('pos.discount');

        return response()->json($cart->snapshot($request));
    }

    public function clearCart(Request $request, CartService $cart): JsonResponse
    {
        $request->session()->forget('pos.cart');
        $request->session()->forget('pos.discount');

        return response()->json($cart->snapshot($request));
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'group' => ['nullable', 'in:food,beverage,alcoholic'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);
        $search = trim($filters['search'] ?? '');
        $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
        $products = Product::query()->with('category')->where('is_active', true)
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$escapedSearch.'%'))
            ->when(! empty($filters['group']), fn ($query) => $query->whereHas(
                'category',
                fn ($category) => $category->where('category_group', $filters['group']),
            ))
            ->when(! empty($filters['category']), fn ($query) => $query->where('category_id', $filters['category']))
            ->orderBy('name')
            ->get();

        return view('pos.index', [
            'products' => $products,
            'categories' => Category::query()
                ->where('is_active', true)
                ->whereNotNull('parent_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function checkout(CheckoutRequest $request, CheckoutService $service, CartService $cart): RedirectResponse
    {
        $data = $request->validated();
        $snapshot = $cart->snapshot($request);
        if (count($snapshot['items']) !== count($request->session()->get('pos.cart', []))) {
            throw ValidationException::withMessages(['items' => 'A product in the cart is no longer available.']);
        }
        // Use the session cart instead of prices submitted by the browser
        $data['items'] = array_map(fn (array $item): array => [
            'product_id' => $item['id'],
            'quantity' => $item['quantity'],
        ], $snapshot['items']);
        if ($data['items'] === []) {
            throw ValidationException::withMessages(['items' => 'The cart is empty.']);
        }
        if ($snapshot['discount_cents'] > 0) {
            abort_unless($request->user()->hasPermission('DiscountApply'), 403);
        }
        $data['cart_discount'] = $request->session()->get('pos.discount', ['type' => 'fixed', 'value' => 0]);
        $sale = $service->checkout($data);

        // Clear the cart only after the sale succeeds
        $request->session()->forget('pos.cart');
        $request->session()->forget('pos.discount');

        return redirect()->route('pos.index')->with('completed_transaction_code', $sale->transaction_code);
    }
}
