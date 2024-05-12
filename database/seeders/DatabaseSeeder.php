<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Game;

class DatabaseSeeder extends Seeder
{        
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // role creation first        
        $this->call(RoleSeeder::class);

        // create admin user
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();       
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('1234'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);       
        $user->assignRole($adminRole);

        // seed of player users
        User::factory()->count(10)->player()->create();

        // seed of games
        Game::factory()->count(50)->create();   

        // update player stadistics
        $this->call(PlayerStadisticSeeder::class);
    }
}
