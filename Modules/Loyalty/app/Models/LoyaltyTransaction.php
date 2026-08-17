<?php

namespace Modules\Loyalty\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Loyalty\Enums\LoyaltyTransactionType;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Invoice;
use Modules\Ordering\Models\Order;
use Modules\Staff\Models\Staff;

/**
 * balance_before/balance_after are deliberately NOT fillable: they're
 * overwritten by trg_loyalty_txn_before_insert regardless of what's sent,
 * but the columns are NOT NULL with no DB default, so something has to be
 * written. The creating() hook below supplies the 0 placeholder the schema
 * asks for, so callers never need to think about it.
 */
#[Fillable([
    'loyalty_account_id', 'customer_id', 'type', 'points', 'order_id',
    'invoice_id', 'reward_redemption_id', 'description', 'created_by',
])]
class LoyaltyTransaction extends Model
{
    public $timestamps = false;

    protected $table = 'kims_loyalty_transactions';

    protected static function booted(): void
    {
        static::creating(function (self $transaction) {
            $transaction->balance_before ??= 0;
            $transaction->balance_after ??= 0;
        });
    }

    protected function casts(): array
    {
        return [
            'type' => LoyaltyTransactionType::class,
            'points' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function loyaltyAccount(): BelongsTo
    {
        return $this->belongsTo(LoyaltyAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function rewardRedemption(): BelongsTo
    {
        return $this->belongsTo(RewardRedemption::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }
}
