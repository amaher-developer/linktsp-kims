<?php

use Laravel\Sanctum\Sanctum;
use Modules\Loyalty\Models\LoyaltyTransaction;
use Modules\Ordering\Models\Customer;

test('customer loyalty account is created on first access and starts at zero', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    $response = $this->getJson(apiUrl('/loyalty'));

    $response->assertOk()->assertJsonPath('data.balance', 0);
});

test('customer can view their loyalty transaction history', function () {
    $customer = Customer::factory()->create();
    $account = $customer->loyaltyAccount()->create(['status' => 'active']);

    LoyaltyTransaction::create([
        'loyalty_account_id' => $account->id,
        'customer_id' => $customer->id,
        'type' => 'earn',
        'points' => 5,
        'description' => 'Test earn',
    ]);

    Sanctum::actingAs($customer);
    $response = $this->getJson(apiUrl('/loyalty/transactions'));

    $response->assertOk()->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.points', 5);
});

