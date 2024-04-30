<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        $user = \App\Models\User::factory()->create([
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player1',
            'email' => 'player1@example.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');

        $user = \App\Models\User::factory()->create([
            'name' => 'Example Player2',
            'email' => 'player2@example.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
    }
}
