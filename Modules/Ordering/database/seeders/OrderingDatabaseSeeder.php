<?php

namespace Modules\Ordering\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Models\OrderItem;

class OrderingDatabaseSeeder extends Seeder
{
    /**
     * @param  array{branch: \Modules\Catalog\Models\Branch, product: \Modules\Catalog\Models\Product}  $catalog
     * @return array{customer: Customer, order: Order}
     */
    public function run(array $catalog): array
    {
        $branch = $catalog['branch'];
        $product = $catalog['product'];

        $customer = Customer::create([
            'first_name' => 'Demo',
            'last_name' => 'Customer',
            'mobile' => '01099999999',
            'email' => 'demo.customer@kims.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $cart = Cart::create([
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'order_type' => 'grab_go',
            'status' => 'checked_out',
            'subtotal' => 65.00,
            'discount_amount' => 0,
            'total_amount' => 74.10,
        ]);

        $order = Order::create([
            'order_number' => 'KIMS-000001',
            'cart_id' => $cart->id,
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'order_type' => 'grab_go',
            'status' => 'confirmed',
            'subtotal' => 65.00,
            'discount_amount' => 0,
            'service_charge' => 0,
            'tax_amount' => 9.10,
            'total_amount' => 74.10,
            'placed_at' => now(),
            'confirmed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_en' => $product->name_en,
            'product_name_ar' => $product->name_ar,
            'quantity' => 1,
            'unit_price' => 65.00,
            'discount_amount' => 0,
            'tax_amount' => 9.10,
            'total_amount' => 74.10,
        ]);

        return ['customer' => $customer, 'order' => $order];
    }
}
