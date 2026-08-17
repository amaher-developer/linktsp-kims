<?php

namespace Modules\Ordering\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Models\OrderItem;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = fake()->randomFloat(2, 20, 100);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'foodics_product_id' => null,
            'product_name_en' => fake()->words(2, true),
            'product_name_ar' => fake()->words(2, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => 0,
            'tax_amount' => round($unitPrice * $quantity * 0.14, 2),
            'total_amount' => round($unitPrice * $quantity * 1.14, 2),
        ];
    }
}
