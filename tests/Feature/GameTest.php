<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Artisan;

class GameTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $guest;
    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('passport:client --name=<client-name> --no-interaction --personal');

        $this->user = User::factory()->create([
            'username' => 'Test User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        $this->guest = User::factory()->create([
            'username' => 'anonymous',
            'email' => 'guest@test.com',
            'password' => Hash::make('password'),
            'role' => 'guest'
        ]);
    }
    #[Test]
    public function test_user_can_throw_dices()
    {
        // Generate a personal access token for the user
        $token = $this->user->createToken('TestToken')->accessToken;

        // Send a POST request to throw dices with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/players/' . $this->user->id . '/games');

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'newGame' => [
                'id',
                'user_id',
                'dice1',
                'dice2',
                'result',
            ]
        ]);

        // Check that the game is in the database
        $this->assertDatabaseHas('games', [
            'user_id' => $this->user->id,
        ]);
    }
    #[Test]
    public function test_user_cannot_throw_dices_for_other_user()
    {
        // Generate a personal access token for the user
        $token = $this->user->createToken('TestToken')->accessToken;

        // Send a POST request to throw dices with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/players/' . $this->guest->id . '/games');

        $response->assertStatus(403);
        $response->assertJson([
            'message' => "You're not authorized to play for this user.",
        ]);
    }

    #[Test]
    public function test_user_can_delete_their_games()
    {
        // Create a game for the user
        Game::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Send a DELETE request to delete all games for this user
        $response = $this->deleteJson("/api/players/{$this->user->id}/games");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => "User's game history has been successfully deleted."
        ]);

        // Check that the games are removed from the database
        $this->assertDatabaseMissing('games', ['user_id' => $this->user->id]);
    }

    /*#[Test]
    public function test_user_cannot_delete_other_user_games()
    {
        // Create a game for another user
        Game::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        // Send a DELETE request trying to delete the games of another user
        $response = $this->deleteJson("/api/players/{$this->otherUser->id}/games");

        $response->assertStatus(403);
        $response->assertJson([
            'message' => "You're not authorized to delete this user's game history.",
        ]);
    }

    #[Test]
    public function test_user_can_get_their_games()
    {
        // Create some games for the user
        Game::factory()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        // Send a GET request to fetch the games
        $response = $this->getJson("/api/players/{$this->user->id}/games");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id', 'username', 'email'
            ],
            'successPercentage',
            'games' => [
                '*' => [
                    'id', 'dice1', 'dice2', 'result'
                ]
            ]
        ]);
    }

    #[Test]
    public function test_user_cannot_get_other_user_games()
    {
        // Create some games for another user
        Game::factory()->count(2)->create([
            'user_id' => $this->otherUser->id,
        ]);

        // Send a GET request trying to fetch the games of another user
        $response = $this->getJson("/api/players/{$this->otherUser->id}/games");

        $response->assertStatus(403);
        $response->assertJson([
            "message" => "You're not authorized to acces this player's profile.",
        ]);
    }*/
}
 