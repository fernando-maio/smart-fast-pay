<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Helpers\AuthDataHelper;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = $this->app->make(AuthController::class);
    }

    public function testRegisterWithValidData()
    {
        $userData = AuthDataHelper::getUserData();
        $response = $this->postJson('/api/auth/register', $userData);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'name',
                'email',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    public function testRegisterWithInvalidData()
    {
        $userData = AuthDataHelper::getUserData(['email' => 'invalid_email']);
        $response = $this->postJson('/api/auth/register', $userData);
        $response->assertStatus(400);
    }

    public function testLoginWithValidCredentials()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $credentials = [
            'email' => 'john@example.com',
            'password' => 'password',
        ];
        $response = $this->postJson('/api/auth/login', $credentials);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    public function testLoginWithInvalidCredentials()
    {
        $credentials = [
            'email' => 'john@example.com',
            'password' => 'wrong_password',
        ];
        $response = $this->postJson('/api/auth/login', $credentials);
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function testMe()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/auth/me');
        
        $response->assertStatus(200)
            ->assertJson([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }

    public function testRefresh()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }
}