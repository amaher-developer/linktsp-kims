<?php

use Laravel\Sanctum\Sanctum;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\Order;

test('checkout turns an active cart into an order with items, payment, and status history', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $branch = Branch::factory()->create();
    $product = Product::factory()->create(['base_price' => 30]);

    $this->postJson(apiUrl('/cart'), ['branch_id' => $branch->id, 'order_type' => 'grab_go'])->assertCreated();
    $this->postJson(apiUrl('/cart/items'), ['product_id' => $product->id, 'quantity' => 2])->assertOk();

    $response = $this->postJson(apiUrl('/cart/checkout'), ['customer_note' => 'No sugar please']);

    $response->assertCreated();
    $response->assertJsonPath('data.status', 'confirmed');
    $response->assertJsonPath('data.total_amount', '60.00');
    $response->assertJsonCount(1, 'data.items');

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect($order->cart->status)->toBe('checked_out');
    expect($order->statusHistory()->count())->toBe(1);
    expect($order->payments()->where('status', 'pending')->exists())->toBeTrue();
});

test('checkout fails when the cart is empty', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $branch = Branch::factory()->create();

    $this->postJson(apiUrl('/cart'), ['branch_id' => $branch->id, 'order_type' => 'grab_go'])->assertCreated();

    $response = $this->postJson(apiUrl('/cart/checkout'));

    $response->assertUnprocessable();
});

test('checkout fails when there is no active cart', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    $response = $this->postJson(apiUrl('/cart/checkout'));

    $response->assertNotFound();
});
