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

class UserTest extends TestCase
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

        $this->guest = User::factory()->create([
            'username' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }
    #[Test]
    public function test_user_can_be_created()
    {
        $this->withoutExceptionHandling();

        $username = $this->faker->unique()->userName;
        $email = $this->faker->unique()->email;

        $response = $this->post('api/players', [
            'username' => $username,
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        //Count the newly registered user from the database
        $this->assertCount(4, User::all());

        // Recheck the database for the newly created user
        $user = User::where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertEquals($user->username, $username);
        $this->assertEquals($user->email, $email);
        $this->assertTrue(Hash::check('password', $user->password));
    }
    #[Test]
    public function test_guest_can_be_created()
    {
        $email = $this->faker->unique()->email;

        $response = $this->post('api/players', [
            'username' => null,
            'email' => $email,
            'password' =>

        ]);
    }
    #[Test]
    public function test_player_can_login()
    {
        $response = $this->post('api/login', [
            'email' => 'user@test.com',
            "password" => 'password'
        ]);

        $response->assertStatus(200);

        $user = User::where("email", "user@test.com")->first();

        $this->assertTrue(Hash::check('password', $user->password));

        // Decode the JSON response data
        $responseData = $response->json();

        // Assert that the response contains the token
        $this->assertArrayHasKey('token', $responseData, "The token is missing from the login response");

        // Assert that the token is not null
        $this->assertNotNull($responseData['token'], "The token is null");
    }
    #[Test]
    public function test_player_can_modify


/*             //modify player's name
            Route::patch("players/{id}", [UserController::class, 'editName']);




            //Player can access their own profile
            Route::get("players/profile", [UserController::class, "profile"]); */

/*                 //Admin-specific routes
    Route::middleware(['role:admin'])->group(function() {
        Route::get("players", [UserController::class, "getAllPlayers"]);
        Route::get("players/ranking", [UserController::class, "getRanking"]);
        Route::get("players/ranking/loser", [UserController::class, "getLoser"]);
        Route::get("players/ranking/winner", [UserController::class, "getWinner"]); */
    

}
