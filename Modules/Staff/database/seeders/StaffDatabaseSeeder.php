<?php

namespace Modules\Staff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Staff\Models\Role;
use Modules\Staff\Models\Staff;

class StaffDatabaseSeeder extends Seeder
{
    /**
     * @return array{roles: \Illuminate\Support\Collection, manager: Staff}
     */
    public function run(): array
    {
        $roles = collect(['cashier', 'barista', 'manager', 'admin'])
            ->mapWithKeys(fn (string $name) => [$name => Role::create(['name' => $name])]);

        $manager = Staff::create([
            'role_id' => $roles['admin']->id,
            'name' => 'KIMS Admin',
            'phone' => '0100000000',
            'email' => 'admin@kims.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return ['roles' => $roles, 'manager' => $manager];
    }
}
