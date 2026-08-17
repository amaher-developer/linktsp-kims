<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\OptionGroup;

/**
 * @extends Factory<OptionGroup>
 */
class OptionGroupFactory extends Factory
{
    protected $model = OptionGroup::class;

    public function definition(): array
    {
        return [
            'foodics_id' => fake()->unique()->numberBetween(1000, 999999),
            'name_en' => fake()->words(2, true),
            'name_ar' => fake()->words(2, true),
            'min_select' => 0,
            'max_select' => 1,
            'is_required' => false,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
