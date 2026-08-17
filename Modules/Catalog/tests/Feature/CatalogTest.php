<?php

use Laravel\Sanctum\Sanctum;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Models\Customer;

test('customer can list active branches with expected shape', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    Branch::factory()->create(['is_active' => true, 'name_en' => 'Downtown']);
    Branch::factory()->create(['is_active' => false, 'name_en' => 'Hidden Branch']);

    $response = $this->getJson(apiUrl('/branches'));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name_en', 'Downtown');
    $response->assertJsonStructure(['data' => [['id', 'name_en', 'name_ar', 'accepts_grab_go', 'accepts_dine_in']]]);
});

test('customer can list categories', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    Category::factory()->count(3)->create(['is_active' => true]);

    $response = $this->getJson(apiUrl('/categories'));

    $response->assertOk()->assertJsonCount(3, 'data');
});

test('product listing exposes branch-specific price and availability when branch_id is given', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    $branch = Branch::factory()->create();
    $product = Product::factory()->create(['base_price' => 50]);
    $product->branches()->attach($branch->id, ['is_available' => true, 'price_override' => 45]);

    $response = $this->getJson(apiUrl("/products?branch_id={$branch->id}"));

    $response->assertOk();
    $response->assertJsonPath('data.0.price', '45.00');
    $response->assertJsonPath('data.0.is_available', true);
});

test('product resource never exposes internal-only fields', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);

    $product = Product::factory()->create();

    $response = $this->getJson(apiUrl("/products/{$product->id}"));

    $response->assertOk();
    $response->assertJsonMissingPath('data.foodics_id');
    $response->assertJsonMissingPath('data.synced_at');
});

test('staff tokens cannot access customer-only catalog routes', function () {
    $staff = staffWithRole('barista');
    Sanctum::actingAs($staff);

    $response = $this->getJson(apiUrl('/branches'));

    $response->assertForbidden();
});
