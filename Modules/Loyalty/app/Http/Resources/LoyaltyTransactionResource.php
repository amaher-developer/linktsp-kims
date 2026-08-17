<?php

namespace Modules\Loyalty\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Loyalty\Models\LoyaltyTransaction */
class LoyaltyTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'points' => $this->points,
            'balance_after' => $this->balance_after,
            'order_id' => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'reward_redemption_id' => $this->reward_redemption_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
