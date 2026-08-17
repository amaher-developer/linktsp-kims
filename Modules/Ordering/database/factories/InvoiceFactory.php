<?php

namespace Modules\Ordering\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Models\Branch;
use Modules\Ordering\Models\Invoice;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'order_id' => null,
            'branch_id' => Branch::factory(),
            'invoice_number' => 'INV-'.$this->faker->unique()->numerify('######'),
            'source' => 'pos',
            'total_amount' => $this->faker->randomFloat(2, 20, 300),
            'issued_at' => now(),
            'verified_at' => null,
        ];
    }
}
