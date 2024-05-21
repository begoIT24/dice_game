<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $playerRole = Role::where('name', 'player')->where('guard_name', 'api')->first();
        return [
            'user_id' => User::role($playerRole)->inRandomOrder()->first()->id,
            'dice1' => $this->faker->numberBetween(1, 6),
            'dice2' => $this->faker->numberBetween(1, 6),
            'win_game' => $this->faker->boolean,
        ];
    }
}
