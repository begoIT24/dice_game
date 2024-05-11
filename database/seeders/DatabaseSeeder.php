<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{    
    protected static ?string $password;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // role creation first        
        $this->call(RoleSeeder::class);

        // create admin user
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();       
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('1234'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);       
        $user->assignRole($adminRole);

        // seed of player users
        \App\Models\User::factory()->count(10)->player()->create();

        // seed of games
        \App\Models\Game::factory()->count(50)->create();   

        // update player stadistics
        $this->call(PlayerStadisticSeeder::class);
    }
}
