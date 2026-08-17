<?php

namespace Modules\Ordering\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 30, 300);

        return [
            'order_number' => 'KIMS-'.fake()->unique()->numerify('######'),
            'cart_id' => Cart::factory(),
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'order_type' => fake()->randomElement(['grab_go', 'dine_in']),
            'status' => 'confirmed',
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'service_charge' => 0,
            'tax_amount' => round($subtotal * 0.14, 2),
            'total_amount' => round($subtotal * 1.14, 2),
            'customer_note' => null,
            'placed_at' => now(),
        ];
    }
}
