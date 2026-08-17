<?php

namespace Modules\Ordering\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ordering\Enums\RefundStatus;
use Modules\Staff\Models\Staff;

#[Fillable([
    'payment_id', 'order_id', 'amount', 'reason', 'initiated_by_type',
    'initiated_by_id', 'approved_by', 'status', 'provider_reference',
    'requested_at', 'completed_at',
])]
class Refund extends Model
{
    use HasFactory;

    protected $table = 'kims_refunds';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => RefundStatus::class,
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }
}
