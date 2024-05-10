<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class PlayerStadisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {           
            $playedGames = $user->games()->count();         
            $wonGames = $user->games()->where('winGame', true)->count();
            $successRate = ($playedGames > 0) ? ($wonGames / $playedGames) * 100 : 0;

            // Update stadistics of players in users table
            $user->update([
                'playedGames' => $playedGames,
                'wonGames' => $wonGames,
                'successRate' => $successRate,
            ]);
        }
    }
}
