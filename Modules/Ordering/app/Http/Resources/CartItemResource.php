<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Ordering\Models\CartItem */
class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name_en' => $this->product?->name_en,
            'product_name_ar' => $this->product?->name_ar,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'options' => CartItemOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
