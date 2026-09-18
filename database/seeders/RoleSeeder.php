<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full system access', 'is_system' => true],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrator with full access', 'is_system' => true],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Manager with limited access', 'is_system' => false],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Staff member with basic access', 'is_system' => false],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
