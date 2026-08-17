<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'foodics_id' => fake()->unique()->numberBetween(1000, 999999),
            'parent_id' => null,
            'name_en' => fake()->words(2, true),
            'name_ar' => fake()->words(2, true),
            'image_url' => null,
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'synced_at' => now(),
        ];
    }
}
