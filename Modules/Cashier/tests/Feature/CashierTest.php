<?php

use Laravel\Sanctum\Sanctum;
use Modules\Loyalty\Models\LoyaltyRule;
use Modules\Loyalty\Models\LoyaltyTransaction;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\CustomerIdentifier;
use Modules\Ordering\Models\Invoice;

test('cashier can identify a customer by QR code', function () {
    $cashier = staffWithRole('cashier');
    $customer = Customer::factory()->create();
    $identifier = CustomerIdentifier::factory()->create(['customer_id' => $customer->id]);

    Sanctum::actingAs($cashier);
    $response = $this->postJson(apiUrl('/cashier/identify-customer'), ['code' => $identifier->value]);

    $response->assertOk()->assertJsonPath('data.id', $customer->id);
});

test('cashier can verify/look up an invoice by number', function () {
    $cashier = staffWithRole('cashier');
    $invoice = Invoice::factory()->create(['invoice_number' => 'INV-000123']);

    Sanctum::actingAs($cashier);
    $response = $this->getJson(apiUrl('/cashier/invoices/INV-000123'));

    $response->assertOk()->assertJsonPath('data.invoice_number', 'INV-000123');
});

test('cashier can award loyalty points for a verifiable invoice using the active loyalty rule', function () {
    $cashier = staffWithRole('cashier');
    $customer = Customer::factory()->create();
    $identifier = CustomerIdentifier::factory()->create(['customer_id' => $customer->id]);
    $invoice = Invoice::factory()->create(['total_amount' => 100]);

    LoyaltyRule::factory()->create([
        'is_active' => true,
        'priority' => 0,
        'earn_points_rate' => 1,
        'earn_amount_unit' => 10,
    ]);

    Sanctum::actingAs($cashier);
    $response = $this->postJson(apiUrl('/cashier/loyalty/award'), [
        'code' => $identifier->value,
        'invoice_number' => $invoice->invoice_number,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.points', 10); // 100 / 10 * 1

    expect($customer->fresh()->loyaltyAccount->balance)->toBe(10);
    expect($invoice->fresh()->verified_at)->not->toBeNull();
});

test('awarding points twice for the same invoice is rejected', function () {
    $cashier = staffWithRole('cashier');
    $customer = Customer::factory()->create();
    $identifier = CustomerIdentifier::factory()->create(['customer_id' => $customer->id]);
    $invoice = Invoice::factory()->create(['total_amount' => 100]);
    $account = $customer->loyaltyAccount()->create(['status' => 'active']);

    LoyaltyRule::factory()->create(['is_active' => true, 'earn_points_rate' => 1, 'earn_amount_unit' => 10]);

    LoyaltyTransaction::create([
        'loyalty_account_id' => $account->id,
        'customer_id' => $customer->id,
        'type' => 'earn',
        'points' => 10,
        'invoice_id' => $invoice->id,
    ]);

    Sanctum::actingAs($cashier);
    $response = $this->postJson(apiUrl('/cashier/loyalty/award'), [
        'code' => $identifier->value,
        'invoice_number' => $invoice->invoice_number,
    ]);

    $response->assertUnprocessable();
});

test('non-cashier staff cannot access cashier endpoints', function () {
    $barista = staffWithRole('barista');
    Sanctum::actingAs($barista);

    $response = $this->postJson(apiUrl('/cashier/identify-customer'), ['code' => 'anything']);

    $response->assertForbidden();
});

test('customers cannot access cashier endpoints', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    $response = $this->postJson(apiUrl('/cashier/identify-customer'), ['code' => 'anything']);

    $response->assertForbidden();
});
