<?php

namespace Modules\Loyalty\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Loyalty\Models\Reward;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
{
    protected $model = Reward::class;

    public function definition(): array
    {
        return [
            'product_id' => null,
            'foodics_product_id' => null,
            'name_en' => fake()->words(3, true),
            'name_ar' => fake()->words(3, true),
            'points_cost' => fake()->numberBetween(50, 500),
            'reward_type' => fake()->randomElement(['product', 'discount']),
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }
}
