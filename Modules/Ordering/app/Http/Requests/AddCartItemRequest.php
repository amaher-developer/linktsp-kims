<?php

namespace Modules\Ordering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Catalog\Models\OptionGroup;
use Modules\Catalog\Models\Product;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:kims_products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'option_ids' => ['sometimes', 'array'],
            'option_ids.*' => ['integer', 'exists:kims_options,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $product = Product::with('optionGroups.options')->find($this->input('product_id'));

            if (! $product) {
                return;
            }

            $selectedOptionIds = collect($this->input('option_ids', []));

            foreach ($product->optionGroups as $group) {
                $selectedInGroup = $selectedOptionIds->intersect($group->options->pluck('id'))->count();

                if ($selectedInGroup < $group->min_select) {
                    $validator->errors()->add('option_ids', "The \"{$group->name_en}\" option group requires at least {$group->min_select} selection(s).");
                }

                if ($selectedInGroup > $group->max_select) {
                    $validator->errors()->add('option_ids', "The \"{$group->name_en}\" option group allows at most {$group->max_select} selection(s).");
                }
            }

            $allowedOptionIds = $product->optionGroups->flatMap(fn (OptionGroup $g) => $g->options->pluck('id'));

            if ($selectedOptionIds->diff($allowedOptionIds)->isNotEmpty()) {
                $validator->errors()->add('option_ids', 'One or more selected options do not belong to this product.');
            }
        });
    }
}
