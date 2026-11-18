<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('InventoryAdjust') === true;
    }

    public function rules(): array
    {
        return ['quantity_change' => ['required', 'integer', 'not_in:0', 'between:-2147483647,2147483647'], 'note' => ['required', 'string', 'max:255']];
    }
}
