<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase; // This ensures migrations run before each test

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_player_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('api/players', [
            'username' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        //Count the newly registered user from the database
        $this->assertCount(1, User::all());

        //Looks for the user just created
        $user = User::first();

        //Compares the values
        $this->assertEquals($user->username, 'Test User');
        $this->assertEquals($user->email, 'user@example.com');
        // Check that password is hashed, not plain
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertEquals($user->role, 'user');
    }
}
