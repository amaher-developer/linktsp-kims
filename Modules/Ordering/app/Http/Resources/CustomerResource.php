<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Loyalty\Http\Resources\LoyaltyAccountResource;

/** @mixin \Modules\Ordering\Models\Customer */
class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'loyalty_account' => new LoyaltyAccountResource($this->whenLoaded('loyaltyAccount')),
            'created_at' => $this->created_at,
        ];
    }
}
