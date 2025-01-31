<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['user', 'authorization' => ['token', 'type']],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function testUserCannotRegisterWithExistingEmail()
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['access_token', 'token_type', 'expires_in', 'user'],
            ]);
    }

    public function testUserCannotLoginWithInvalidCredentials()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false, 'error' => 'Não autorizado']);
    }

    public function testAuthenticatedUserCanAccessProtectedRoute()
    {
        $user = User::factory()->create();
        $token = Auth::guard('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/user');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'item' => ['id' => $user->id, 'email' => $user->email]]);
    }

    public function testAuthenticatedUserCanLogout()
    {
        $user = User::factory()->create();
        $token = Auth::guard('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Logout realizado com sucesso']);
    }

    public function testAuthenticatedUserCanRefreshToken()
    {
        $user = User::factory()->create();
        $token = Auth::guard('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/v1/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['access_token', 'token_type', 'expires_in', 'user'],
            ]);
    }
}
