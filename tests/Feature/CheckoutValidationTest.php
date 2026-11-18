<?php

use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\Validator;

it('requires an alphanumeric GCash reference', function (): void {
    $rules = (new CheckoutRequest)->rules();
    $validator = Validator::make(
        ['payment_method' => 'gcash'],
        ['payment_method' => $rules['payment_method'], 'gcash_reference' => $rules['gcash_reference']],
    );
    expect($validator->fails())->toBeTrue();
    $validator = Validator::make(
        ['payment_method' => 'gcash', 'gcash_reference' => 'ABC123'],
        ['payment_method' => $rules['payment_method'], 'gcash_reference' => $rules['gcash_reference']],
    );
    expect($validator->passes())->toBeTrue();
    $validator = Validator::make(
        ['payment_method' => 'gcash', 'gcash_reference' => 'ABC-123'],
        ['payment_method' => $rules['payment_method'], 'gcash_reference' => $rules['gcash_reference']],
    );
    expect($validator->fails())->toBeTrue();
});

it('rejects card payments', function (): void {
    $rules = (new CheckoutRequest)->rules();
    expect(Validator::make(['payment_method' => 'card'], ['payment_method' => $rules['payment_method']])->fails())->toBeTrue();
});
