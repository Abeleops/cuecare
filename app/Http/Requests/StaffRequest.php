<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('StaffManage') === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['full_name' => preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $this->input('full_name'))]);
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $passwordRule = Password::min(10);
        if (app()->isProduction()) {
            $passwordRule->uncompromised();
        }

        return [
            'full_name' => ['required', 'string', 'max:150'],
            'username' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user),
            ],
            'email' => ['nullable', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::exists('roles', 'key')],
            'password' => [$user ? 'nullable' : 'required', 'different:username', $passwordRule],
            'avatar' => [
                'nullable',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp'])
                    ->max(2048)
                    ->dimensions(Rule::dimensions()->maxWidth(4096)->maxHeight(4096)),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
