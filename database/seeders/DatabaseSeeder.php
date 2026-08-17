<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Database\Seeders\CatalogDatabaseSeeder;
use Modules\Integration\Database\Seeders\IntegrationDatabaseSeeder;
use Modules\Loyalty\Database\Seeders\LoyaltyDatabaseSeeder;
use Modules\Ordering\Database\Seeders\OrderingDatabaseSeeder;
use Modules\Staff\Database\Seeders\StaffDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $staff = (new StaffDatabaseSeeder())->run();
        $catalog = (new CatalogDatabaseSeeder())->run();

        $staff['manager']->branches()->attach($catalog['branch']->id);

        $ordering = (new OrderingDatabaseSeeder())->run($catalog);

        (new LoyaltyDatabaseSeeder())->run([...$catalog, ...$ordering]);

        (new IntegrationDatabaseSeeder())->run();
    }
}
