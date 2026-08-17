<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Ordering\Models\OrderItemOption */
class OrderItemOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'option_group_name_en' => $this->option_group_name_en,
            'option_group_name_ar' => $this->option_group_name_ar,
            'option_name_en' => $this->option_name_en,
            'option_name_ar' => $this->option_name_ar,
            'price_delta' => $this->price_delta,
        ];
    }
}
