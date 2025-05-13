<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // İzinler
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view products',
            'create products',
            'edit products',
            'delete products',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Roller
        $adminRole = Role::create(['name' => 'admin']);
        $sellerRole = Role::create(['name' => 'seller']);

        // Roller ile izinleri ilişkilendir
        $adminRole->givePermissionTo(Permission::all());
        $sellerRole->givePermissionTo(['view products', 'create products']);


    }
}
