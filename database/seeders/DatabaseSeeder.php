<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // role creation first
        $this->call(RoleSeeder::class);

        // create demo users
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();
        $user = \App\Models\User::factory()->create([
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
            'password' => '1234'
        ]);       
        $user->assignRole($adminRole);

        $playerRole = Role::where('name', 'player')->where('guard_name', 'api')->first();
        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player1',
            'email' => 'player1@example.com',
            'password' => '1234'
        ]);
        $user->assignRole($playerRole);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player2',
            'email' => 'player2@example.com',
            'password' => '1234'
        ]);
        $user->assignRole($playerRole);
    }
}
