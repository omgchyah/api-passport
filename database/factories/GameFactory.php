<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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

        $dice1 = fake()->numberBetween(1, 6);
        $dice2 = fake()->numberBetween(1, 6);
        $result = ($dice1 + $dice2) == 7 ? 'w' : 'l';

        return [
            'user_id' => User::factory(),
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $result
        ];
    }
}
