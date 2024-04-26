<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name' => 'Admin']);
        $rolePlayer = Role::create(['name' => 'Player']);

         // Reset cached roles and permissions (durante desarrollo de la app)
         app()[PermissionRegistrar::class]->forgetCachedPermissions();

         /*create permissions
        Permission::create(['name' => 'signup'])->syncRoles([$roleAdmin, $rolePlayer]); //assignRole() si es 1
        Permission::create(['name' => 'login'])->syncRoles([$roleAdmin, $rolePlayer]);
        Permission::create(['name' => 'logout'])->syncRoles([$roleAdmin, $rolePlayer]);
        Permission::create(['name' => 'user'])->syncRoles([$roleAdmin, $rolePlayer]);
         */

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($roleAdmin);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player',
            'email' => 'player@example.com',
        ]);
        $user->assignRole($rolePlayer);
    }
}
