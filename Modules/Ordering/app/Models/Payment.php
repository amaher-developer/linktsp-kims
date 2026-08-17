<?php

namespace Modules\Ordering\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ordering\Enums\PaymentStatus;

#[Fillable([
    'order_id', 'provider', 'method', 'amount', 'currency', 'transaction_id',
    'provider_reference', 'status', 'paid_at', 'failed_at',
])]
class Payment extends Model
{
    use HasFactory;

    protected $table = 'kims_payments';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
