<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $editingProduct = $this->route('product');
        $this->errorBag = $editingProduct ? 'productEdit' : 'productCreate';
        $this->merge(['_edit_id' => $editingProduct?->getKey()]);
    }

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('ProductsManage') === true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at')->where('is_active', true),
            ],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock_quantity' => [
                $this->isMethod('post') ? 'required' : 'required_with:original_stock',
                Rule::prohibitedIf(! $this->isMethod('post') && ! $this->has('original_stock')),
                'integer',
                'min:0',
                'max:2147483647',
            ],
            'original_stock' => [
                Rule::requiredIf(! $this->isMethod('post') && $this->has('stock_quantity')),
                'integer',
                'min:0',
                'max:2147483647',
            ],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'remove_image' => ['sometimes', 'boolean'],
            'image' => [
                'nullable',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp'])
                    ->max(2048)
                    ->dimensions(Rule::dimensions()->maxWidth(4096)->maxHeight(4096)),
            ],
        ];
    }
}
