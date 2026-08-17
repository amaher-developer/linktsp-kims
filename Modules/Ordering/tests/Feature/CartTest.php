<?php

use Laravel\Sanctum\Sanctum;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\Option;
use Modules\Catalog\Models\OptionGroup;
use Modules\Catalog\Models\Product;
use Modules\Ordering\Models\Customer;

function productWithSizeOptions(): Product
{
    $product = Product::factory()->create(['base_price' => 50]);

    $group = OptionGroup::factory()->create(['min_select' => 1, 'max_select' => 1, 'is_required' => true]);
    $small = Option::factory()->create(['option_group_id' => $group->id, 'price_delta' => 0]);
    $large = Option::factory()->create(['option_group_id' => $group->id, 'price_delta' => 10]);

    $product->optionGroups()->attach($group->id, ['sort_order' => 1]);

    return $product->setRelation('optionGroups', collect([$group->load('options')]));
}

test('customer can start a cart for a branch', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $branch = Branch::factory()->create();

    $response = $this->postJson(apiUrl('/cart'), [
        'branch_id' => $branch->id,
        'order_type' => 'grab_go',
    ]);

    $response->assertCreated()->assertJsonPath('data.branch_id', $branch->id)->assertJsonPath('data.status', 'active');
});

test('customer can add an item with a required option selection and totals are computed', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $branch = Branch::factory()->create();
    $product = productWithSizeOptions();
    $largeOption = $product->optionGroups->first()->options->firstWhere('price_delta', 10);

    $this->postJson(apiUrl('/cart'), ['branch_id' => $branch->id, 'order_type' => 'grab_go'])->assertCreated();

    $response = $this->postJson(apiUrl('/cart/items'), [
        'product_id' => $product->id,
        'quantity' => 2,
        'option_ids' => [$largeOption->id],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.subtotal', '120.00'); // (50 + 10) * 2
    $response->assertJsonCount(1, 'data.items');
    $response->assertJsonPath('data.items.0.options.0.price_delta', '10.00');
});

test('adding an item without a required option selection fails validation', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $branch = Branch::factory()->create();
    $product = productWithSizeOptions();

    $this->postJson(apiUrl('/cart'), ['branch_id' => $branch->id, 'order_type' => 'grab_go'])->assertCreated();

    $response = $this->postJson(apiUrl('/cart/items'), [
        'product_id' => $product->id,
        'quantity' => 1,
        'option_ids' => [],
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('option_ids');
});

test('a customer cannot modify another customer\'s cart item', function () {
    $owner = Customer::factory()->create();
    $intruder = Customer::factory()->create();
    $branch = Branch::factory()->create();
    $product = Product::factory()->create();

    Sanctum::actingAs($owner);
    $this->postJson(apiUrl('/cart'), ['branch_id' => $branch->id, 'order_type' => 'grab_go'])->assertCreated();
    $item = $this->postJson(apiUrl('/cart/items'), ['product_id' => $product->id, 'quantity' => 1])
        ->json('data.items.0');

    Sanctum::actingAs($intruder);
    $response = $this->deleteJson(apiUrl("/cart/items/{$item['id']}"));

    $response->assertForbidden();
});

test('cart requires an active cart before items can be added', function () {
    $customer = Customer::factory()->create();
    Sanctum::actingAs($customer);
    $product = Product::factory()->create();

    $response = $this->postJson(apiUrl('/cart/items'), ['product_id' => $product->id, 'quantity' => 1]);

    $response->assertNotFound();
});

