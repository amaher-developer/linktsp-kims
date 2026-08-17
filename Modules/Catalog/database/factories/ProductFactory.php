<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'foodics_id' => fake()->unique()->numberBetween(1000, 999999),
            'category_id' => Category::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-#####')),
            'name_en' => fake()->words(2, true),
            'name_ar' => fake()->words(2, true),
            'description_en' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
            'image_url' => null,
            'base_price' => fake()->randomFloat(2, 20, 200),
            'is_available' => true,
            'is_active' => true,
            'synced_at' => now(),
        ];
    }
}
