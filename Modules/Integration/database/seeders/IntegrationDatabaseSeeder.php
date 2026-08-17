<?php

namespace Modules\Integration\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Integration\Models\Integration;

class IntegrationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Integration::create([
            'provider' => 'foodics',
            'name' => 'Foodics',
            'status' => 'inactive',
            'credentials' => json_encode([]),
        ]);
    }
}
