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
            $wonGames = $user->games()->where('win_game', true)->count();
            $successRate = ($playedGames > 0) ? ($wonGames / $playedGames) * 100 : 0;

            // Update stadistics of players in users table
            $user->update([
                'played_games' => $playedGames,
                'won_games' => $wonGames,
                'success_rate' => $successRate,
            ]);
        }
    }
}
