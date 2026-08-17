<?php

namespace Modules\Ordering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:kims_branches,id'],
            'order_type' => ['required', 'in:grab_go,dine_in'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
