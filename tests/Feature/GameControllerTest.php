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

class GameControllerTest extends TestCase
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
}
