<?php

namespace Modules\Ordering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
