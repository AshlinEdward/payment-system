<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePayInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'customer_reference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
