<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Branch;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $city = fake()->city();

        return [
            'foodics_id' => fake()->unique()->numberBetween(1000, 999999),
            'name_en' => 'KIMS '.$city,
            'name_ar' => 'كيمز '.$city,
            'code' => strtoupper(fake()->unique()->bothify('BR-###')),
            'address' => fake()->streetAddress(),
            'city' => $city,
            'latitude' => fake()->latitude(29, 31),
            'longitude' => fake()->longitude(30, 32),
            'phone' => fake()->numerify('01#########'),
            'accepts_grab_go' => true,
            'accepts_dine_in' => true,
            'is_active' => true,
            'synced_at' => now(),
        ];
    }
}
