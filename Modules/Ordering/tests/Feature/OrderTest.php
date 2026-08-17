<?php

use Laravel\Sanctum\Sanctum;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;

test('customer can list only their own orders', function () {
    $customer = Customer::factory()->create();
    $otherCustomer = Customer::factory()->create();
    $branch = Branch::factory()->create();

    Order::factory()->create([
        'customer_id' => $customer->id,
        'cart_id' => Cart::factory()->create(['customer_id' => $customer->id, 'branch_id' => $branch->id])->id,
        'branch_id' => $branch->id,
    ]);
    Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'cart_id' => Cart::factory()->create(['customer_id' => $otherCustomer->id, 'branch_id' => $branch->id])->id,
        'branch_id' => $branch->id,
    ]);

    Sanctum::actingAs($customer);
    $response = $this->getJson(apiUrl('/orders'));

    $response->assertOk()->assertJsonCount(1, 'data');
});

test('customer cannot view another customer\'s order', function () {
    $customer = Customer::factory()->create();
    $otherCustomer = Customer::factory()->create();
    $branch = Branch::factory()->create();

    $order = Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'cart_id' => Cart::factory()->create(['customer_id' => $otherCustomer->id, 'branch_id' => $branch->id])->id,
        'branch_id' => $branch->id,
    ]);

    Sanctum::actingAs($customer);
    $response = $this->getJson(apiUrl("/orders/{$order->id}"));

    $response->assertForbidden();
});
