<?php

namespace Modules\Ordering\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Customer;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'order_type' => fake()->randomElement(['grab_go', 'dine_in']),
            'status' => 'checked_out',
            'subtotal' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'note' => null,
            'expires_at' => null,
        ];
    }
}
