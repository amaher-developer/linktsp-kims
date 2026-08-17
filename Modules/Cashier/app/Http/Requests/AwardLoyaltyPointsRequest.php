<?php

namespace Modules\Cashier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardLoyaltyPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'invoice_number' => ['required', 'string', 'exists:kims_invoices,invoice_number'],
        ];
    }
}
