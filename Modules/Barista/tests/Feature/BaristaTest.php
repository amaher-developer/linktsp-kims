<?php

use Laravel\Sanctum\Sanctum;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Order;

test('barista sees only orders from their assigned branches', function () {
    $barista = staffWithRole('barista');
    $ownBranch = Branch::factory()->create();
    $otherBranch = Branch::factory()->create();
    $barista->branches()->attach($ownBranch->id);

    Order::factory()->create([
        'branch_id' => $ownBranch->id,
        'cart_id' => Cart::factory()->create(['branch_id' => $ownBranch->id])->id,
    ]);
    Order::factory()->create([
        'branch_id' => $otherBranch->id,
        'cart_id' => Cart::factory()->create(['branch_id' => $otherBranch->id])->id,
    ]);

    Sanctum::actingAs($barista);
    $response = $this->getJson(apiUrl('/barista/orders'));

    $response->assertOk()->assertJsonCount(1, 'data');
});

test('barista can update the status of an order at their branch', function () {
    $barista = staffWithRole('barista');
    $branch = Branch::factory()->create();
    $barista->branches()->attach($branch->id);

    $order = Order::factory()->create([
        'branch_id' => $branch->id,
        'cart_id' => Cart::factory()->create(['branch_id' => $branch->id])->id,
        'status' => 'confirmed',
    ]);

    Sanctum::actingAs($barista);
    $response = $this->putJson(apiUrl("/barista/orders/{$order->id}/status"), ['status' => 'preparing']);

    $response->assertOk()->assertJsonPath('data.status', 'preparing');
    expect($order->statusHistory()->count())->toBe(1);
});

test('barista cannot update an order outside their assigned branches', function () {
    $barista = staffWithRole('barista');
    $ownBranch = Branch::factory()->create();
    $otherBranch = Branch::factory()->create();
    $barista->branches()->attach($ownBranch->id);

    $order = Order::factory()->create([
        'branch_id' => $otherBranch->id,
        'cart_id' => Cart::factory()->create(['branch_id' => $otherBranch->id])->id,
    ]);

    Sanctum::actingAs($barista);
    $response = $this->putJson(apiUrl("/barista/orders/{$order->id}/status"), ['status' => 'preparing']);

    $response->assertForbidden();
});

test('cashier cannot access barista endpoints', function () {
    $cashier = staffWithRole('cashier');
    Sanctum::actingAs($cashier);

    $response = $this->getJson(apiUrl('/barista/orders'));

    $response->assertForbidden();
});
