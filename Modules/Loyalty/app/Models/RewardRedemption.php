<?php

namespace Modules\Loyalty\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Loyalty\Enums\RewardRedemptionStatus;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;
use Modules\Staff\Models\Staff;

#[Fillable([
    'customer_id', 'loyalty_account_id', 'reward_id', 'order_id',
    'points_cost', 'status', 'redemption_code', 'redeemed_at',
    'cancelled_at', 'created_by',
])]
class RewardRedemption extends Model
{
    public $timestamps = false;

    protected $table = 'kims_reward_redemptions';

    protected function casts(): array
    {
        return [
            'points_cost' => 'integer',
            'status' => RewardRedemptionStatus::class,
            'redeemed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function loyaltyAccount(): BelongsTo
    {
        return $this->belongsTo(LoyaltyAccount::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function loyaltyTransaction(): HasOne
    {
        return $this->hasOne(LoyaltyTransaction::class, 'reward_redemption_id');
    }
}
