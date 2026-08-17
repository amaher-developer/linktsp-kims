<?php

namespace Modules\Integration\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Integration\Models\Integration;

/**
 * @extends Factory<Integration>
 */
class IntegrationFactory extends Factory
{
    protected $model = Integration::class;

    public function definition(): array
    {
        return [
            'provider' => 'foodics',
            'name' => 'Foodics',
            'status' => 'inactive',
            'credentials' => json_encode([]),
            'settings' => null,
            'last_synced_at' => null,
        ];
    }
}
