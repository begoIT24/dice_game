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
        // Reset cached roles and permissions (during web development)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roleAdmin = Role::create(['name' => 'Admin', 'guard_name' => 'api']);      // guard-name = api: only users authenticated (auth.php)
        $rolePlayer = Role::create(['name' => 'Player', 'guard_name' => 'api']);

         /*create permissions
        Permission::create(['name' => 'signup'])->syncRoles([$roleAdmin, $rolePlayer]); //assignRole() for only 1
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
            'name' => 'Example Player1',
            'email' => 'player1@example.com',
        ]);
        $user->assignRole($rolePlayer);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player2',
            'email' => 'player2@example.com',
        ]);
        $user->assignRole($rolePlayer);
    }
}
