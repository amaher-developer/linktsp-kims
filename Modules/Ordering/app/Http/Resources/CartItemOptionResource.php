<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Ordering\Models\CartItemOption */
class CartItemOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'option_group_id' => $this->option_group_id,
            'option_id' => $this->option_id,
            'name_en' => $this->option?->name_en,
            'name_ar' => $this->option?->name_ar,
            'price_delta' => $this->price_delta,
        ];
    }
}
