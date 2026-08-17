<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Http\Resources\BranchResource;

/** @mixin \Modules\Ordering\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'order_type' => $this->order_type,
            'status' => $this->status->value,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'service_charge' => $this->service_charge,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'customer_note' => $this->customer_note,
            'placed_at' => $this->placed_at,
            'confirmed_at' => $this->confirmed_at,
            'preparing_at' => $this->preparing_at,
            'ready_at' => $this->ready_at,
            'collected_at' => $this->collected_at,
            'cancelled_at' => $this->cancelled_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
