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
        //app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'api']); 
        $rolePlayer = Role::create(['name' => 'player', 'guard_name' => 'api']);

        //AuthController permissions
        Permission::create(['name' => 'login management', 'guard_name' => 'api'])->syncRoles([$roleAdmin, $rolePlayer]); //Permission 1
        // Permission::create(['name' => 'signup'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'login'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'logout'])->syncRoles([$roleAdmin, $rolePlayer]);
        // Permission::create(['name' => 'user'])->syncRoles([$roleAdmin, $rolePlayer]);

        //UserController permissions
        Permission::create(['name' => 'update name', 'guard_name' => 'api'])->assignRole([$rolePlayer]);                 //Permission 2     
        Permission::create(['name' => 'players information', 'guard_name' => 'api'])->assignRole([$roleAdmin]);         //Permission 3
       
        //GameController permissions
        Permission::create(['name' => 'game actions', 'guard_name' => 'api'])->assignRole([$rolePlayer]);              //Permission 4        
    }
}
