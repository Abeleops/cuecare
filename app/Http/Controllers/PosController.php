<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;

class PosController extends Controller
{
    public function checkout(CheckoutRequest $request, CheckoutService $service): RedirectResponse
    {
        $sale = $service->checkout($request->validated());
        return redirect()->route('pos.index')->with('sale_id', $sale->id);
    }
}
