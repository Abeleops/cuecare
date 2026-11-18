<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoidSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('SalesVoid') === true;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:255']];
    }
}
