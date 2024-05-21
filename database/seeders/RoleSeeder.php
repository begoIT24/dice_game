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
       
        //UserController permissions
        Permission::create(['name' => 'update_name', 'guard_name' => 'api'])->assignRole([$rolePlayer]);         //Permission 1
        Permission::create(['name' => 'players_information', 'guard_name' => 'api'])->assignRole([$roleAdmin]);  //Permission 2
       
        //GameController permissions
        Permission::create(['name' => 'game_actions', 'guard_name' => 'api'])->assignRole([$rolePlayer]);       //Permission 3    
    }
}
