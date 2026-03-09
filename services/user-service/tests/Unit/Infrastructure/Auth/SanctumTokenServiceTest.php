<?php

namespace Tests\Unit\Infrastructure\Auth;

use App\Domain\Services\TokenServiceInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; 

class SanctumTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_token(): void
    {
        $user = User::factory()->create();

        $tokenService = $this->app->make(TokenServiceInterface::class);
        $token = $tokenService->generateToken($user);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }
}