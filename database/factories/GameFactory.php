<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Game;

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

/*         $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->integer('dice_1');
        $table->integer('dice_2');
        $table->enum('result', ['w', 'l']);
        $table->timestamps(); */







        return [
            'user_id'
        ];
    }
}
