<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer.name' => ['required', 'string', 'max:120'],
            'customer.email' => ['nullable', 'email'],
            'customer.phone' => ['required', 'string', 'max:20'],
            'customer.city' => ['required', 'string', 'max:80'],
            'customer.address' => ['required', 'string', 'max:500'],
            'customer.notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
