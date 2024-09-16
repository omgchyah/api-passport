<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
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

        $this->admin = User::factory()->create([
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
            'password' => Hash::make('password')
        ]);

        $response->assertStatus(201);

        //Count the newly registered user from the database
        $this->assertCount(4, User::all());

        $user = User::where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertEquals($user->username, 'anonymous');
        $this->assertEquals($user->role, 'guest');
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
    public function test_player_can_modify_name()
    {
        // Generate a personal access token for the user
        $token = $this->user->createToken('TestToken')->accessToken;

        // New username to update
        $newUsername = 'Updated Test User';

        // Send a PATCH request to update the username with a Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch("api/players/{$this->user->id}", [
            'username' => $newUsername,
        ]);

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Check that the response contains the success message
        $response->assertJson([
            'message' => 'Username changed successfully.',
        ]);

        // Refresh the user instance to get the updated values from the database
        $this->user->refresh();

        // Assert that the username was updated in the database
        $this->assertEquals($newUsername, $this->user->username);

    }
    #[Test]
    public function test_player_can_access_profile()
    {
        // Create a token for the authenticated user
        $token = $this->user->createToken('TestToken')->accessToken;

        // Send a GET request to the profile route with the Bearer token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/players/profile');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the user's profile data
        $response->assertJson([
            'data' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'role' => $this->user->role,
                'email' => $this->user->email,
                'email_verified_at' => $this->user->email_verified_at->toISOString(),
                'created_at' => $this->user->created_at->toISOString(),
                'updated_at' => $this->user->updated_at->toISOString(),
            ]
        ]);
    }
    #[Test]
    public function test_admin_can_see_all_players()
    {
        $token = $this->admin->createToken('adminToken')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('api/players');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'players' => [
                '*' => [
                    'id',
                    'username',
                    'email',
                    'successPercentage'
                ]
            ]
        ]);

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'username' => $this->user->username,
            'email' => $this->user->email,
        ]);

        $response->assertJsonFragment([
            'id' => $this->guest->id,
            'username' => $this->guest->username,
            'email' => $this->guest->email,
        ]);
    }
    #[Test]
    public function test_admin_can_get_ranking()
    {
        $token = $this->admin->createToken('adminToken')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('api/players/ranking');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'totalAverageSuccessPercentage',
        ]);
    }
    #[Test]
    public function test_admin_can_see_loser()
    {
        $token = $this->admin->createToken('adminToken')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('api/players/ranking/loser');

        $response->assertStatus(200);

        // Decode the JSON response
        $responseData = $response->json();

        // Check for either single 'loser' or multiple 'losers'
        if (array_key_exists('loser', $responseData)) {
            // Assert structure for single loser
            $response->assertJsonStructure([
                'successPercentage',
                'loser' => [
                    'id',
                    'username',
                    'email',
                ]
            ]);
        } elseif (array_key_exists('losers', $responseData)) {
            // Assert structure for multiple losers
            $response->assertJsonStructure([
                'successPercentage',
                'losers' => [
                    '*' => [
                        'id',
                        'username',
                        'email',
                    ]
                ]
            ]);
        } else {
            $this->fail('Neither "loser" nor "losers" key found in the response.');
        }
    }
    #[Test]
    public function test_admin_can_see_winner()
    {
        $token = $this->admin->createToken('adminToken')->accessToken;
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('api/players/ranking/winner');
    
        $response->assertStatus(200);

        // Decode the JSON response
        $responseData = $response->json();

        // Check for either single 'winner' or multiple 'winners'
        if (array_key_exists('winner', $responseData)) {
            // Assert structure for single winner
            $response->assertJsonStructure([
                'successPercentage',
                'winner' => [
                    'id',
                    'username',
                    'email',
                ]
            ]);
        } elseif (array_key_exists('winners', $responseData)) {
            // Assert structure for multiple winners
            $response->assertJsonStructure([
                'successPercentage',
                'winners' => [
                    '*' => [
                        'id',
                        'username',
                        'email',
                    ]
                ]
            ]);
        } else {
            $this->fail('Neither "winner" nor "winners" key found in the response.');
        }  
    }
    #[Test]
    public function test_user_cant_see_all_players()
    {
        $token = $this->user->createToken('userToken')->accessToken;
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('api/players');
    
        $response->assertStatus(403);
    }


}
