<?php

namespace Modules\Ordering\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Ordering\Models\Customer;
use Modules\Ordering\Models\CustomerIdentifier;

/**
 * @extends Factory<CustomerIdentifier>
 */
class CustomerIdentifierFactory extends Factory
{
    protected $model = CustomerIdentifier::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => 'qr',
            'value' => strtoupper($this->faker->unique()->bothify('QR-########')),
            'is_primary' => true,
            'is_active' => true,
        ];
    }
}
