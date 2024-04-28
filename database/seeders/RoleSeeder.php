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

        $roleAdmin = Role::create(['name' => 'admin']);      // 'guard_name' => 'api: only users authenticated (auth.php)
        $rolePlayer = Role::create(['name' => 'player']);

        Permission::create(['name' => 'login management'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'signup'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'login'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'logout'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'user'])->syncRoles([$roleAdmin, $rolePlayer]);

        Permission::create(['name' => 'update name'])->syncRoles([$roleAdmin, $rolePlayer]);
        
        Permission::create(['name' => 'players information'])->assignRole([$roleAdmin]);
       
        Permission::create(['name' => 'game actions'])->assignRole([$rolePlayer]);
       
        // Permission::create(['name' => 'playGame'])->assignRole([$rolePlayer]);
        // Permission::create(['name' => 'deletePlayerGames'])->assignRole([$rolePlayer]);
        // Permission::create(['name' => 'showPlayerGames'])->assignRole([$rolePlayer]);





       
    }
}
