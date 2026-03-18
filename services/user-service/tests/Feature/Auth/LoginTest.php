<?php

namespace Tests\Feature\Auth;

use App\Exceptions\Handler;
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeFactoryUser(bool $isActive = true): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_active' => $isActive
        ]);
    }

    public function test_user_can_login() : void 
    {
        $this->makeFactoryUser();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'access_token',
                    'token_type',
                ]
            ]);
    }

    public function test_login_email_is_require() : void 
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => 'password'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_password_is_require() : void 
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_email_must_be_valid() : void 
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_invalid_password() : void 
    {
        $this->makeFactoryUser();

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials provided'
            ]);
    }

    public function test_login_user_not_found() : void 
    {
        $response = $this->postJson('/api/login', [
            'email' => 'missing@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials provided'
            ]);
    }

    public function test_login_fails_with_inactive_account() : void 
    {
        $this->makeFactoryUser(false);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'User account is inactive'
            ]);
    }
}