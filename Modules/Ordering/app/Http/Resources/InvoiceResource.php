<?php

namespace Modules\Ordering\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Ordering\Models\Invoice */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'branch_id' => $this->branch_id,
            'source' => $this->source,
            'total_amount' => $this->total_amount,
            'issued_at' => $this->issued_at,
            'verified_at' => $this->verified_at,
            'order_id' => $this->order_id,
        ];
    }
}
