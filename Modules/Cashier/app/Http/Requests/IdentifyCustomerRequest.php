<?php

namespace Modules\Cashier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdentifyCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
        ];
    }
}
