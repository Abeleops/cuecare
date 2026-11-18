<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('PosAccess') === true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')->whereNull('deleted_at')->where('is_active', true)],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'payment_method' => ['required', Rule::in(['cash', 'gcash'])],
            'amount_received' => ['required', 'numeric', 'min:0'],
            'gcash_reference' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9]+$/'],
            'customer_id' => [Rule::exists('customers', 'id')->whereNull('deleted_at'), 'nullable', 'integer'],
            'order_type' => ['required', Rule::in(['dine_in', 'takeout'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
