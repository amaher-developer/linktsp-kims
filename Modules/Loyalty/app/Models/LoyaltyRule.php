<?php

namespace Modules\Loyalty\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'priority', 'earn_points_rate', 'earn_amount_unit',
    'redeem_points_unit', 'redeem_value', 'minimum_redeem_points',
    'is_active', 'starts_at', 'ends_at',
])]
class LoyaltyRule extends Model
{
    use HasFactory;

    protected $table = 'kims_loyalty_rules';

    protected function casts(): array
    {
        return [
            'earn_points_rate' => 'decimal:4',
            'earn_amount_unit' => 'decimal:4',
            'redeem_points_unit' => 'decimal:4',
            'redeem_value' => 'decimal:4',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * The highest-priority rule currently in effect, per the schema's own
     * "higher wins when more than one rule is active for the same date"
     * comment (kims_schema.sql section 8).
     */
    public static function currentlyActive(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderByDesc('priority')
            ->first();
    }

    public function pointsForAmount(float $amount): int
    {
        if ((float) $this->earn_amount_unit <= 0) {
            return 0;
        }

        return (int) floor($amount / (float) $this->earn_amount_unit * (float) $this->earn_points_rate);
    }
}
