<?php

namespace Modules\Loyalty\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Loyalty\Models\LoyaltyRule;

/**
 * @extends Factory<LoyaltyRule>
 */
class LoyaltyRuleFactory extends Factory
{
    protected $model = LoyaltyRule::class;

    public function definition(): array
    {
        return [
            'name' => 'Standard Earn & Redeem',
            'priority' => 0,
            'earn_points_rate' => 1,
            'earn_amount_unit' => 10,
            'redeem_points_unit' => 100,
            'redeem_value' => 10,
            'minimum_redeem_points' => 100,
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }
}
