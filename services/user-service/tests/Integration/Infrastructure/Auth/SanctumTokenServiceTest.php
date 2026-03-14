<?php

namespace Tests\Integration\Infrastructure\Auth;

use App\Domain\Services\TokenServiceInterface;
use App\Infrastructure\EloquentUserMapper;
use App\Models\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; 

class SanctumTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_token(): void
    {
        $eloquentUser = EloquentUser::factory()->create();
        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $tokenService = $this->app->make(TokenServiceInterface::class);
        $token = $tokenService->generateToken($domainUser);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }
}