<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('CustomersManage') === true;
    }

    protected function prepareForValidation(): void
    {
        $phone = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $this->input('phone')));
        $email = trim((string) $this->input('email'));
        // Store missing contact details as null, not empty strings
        $this->merge([
            'full_name' => trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $this->input('full_name'))),
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'email' => [
                'nullable',
                'email:rfc',
                'max:150',
                Rule::unique('customers', 'email')->whereNull('deleted_at')->ignore($this->route('customer')),
            ],
        ];
    }
}
