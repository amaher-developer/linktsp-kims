<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'option_group_id' => OptionGroup::factory(),
            'foodics_id' => fake()->unique()->numberBetween(1000, 999999),
            'name_en' => fake()->word(),
            'name_ar' => fake()->word(),
            'price_delta' => fake()->randomFloat(2, 0, 20),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
