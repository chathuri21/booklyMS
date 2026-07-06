<?php

namespace Tests\Integration\Infrastructure\Auth;

use App\Infrastructure\Auth\JwtTokenService;
use App\Infrastructure\EloquentUserMapper;
use App\Models\User as EloquentUser;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_token_contains_expected_claims(): void
    {
        $eloquentUser = EloquentUser::factory()->create();
        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $token = (new JwtTokenService())->generateToken($domainUser);

        $claims = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algo')));

        $this->assertSame($domainUser->id, $claims->sub);
        $this->assertSame($domainUser->name, $claims->name);
        $this->assertSame($domainUser->email, $claims->email);
        $this->assertSame($domainUser->role, $claims->role);
        $this->assertGreaterThan(time(), $claims->exp);
    }

    public function test_token_signed_with_wrong_secret_is_rejected(): void
    {
        $eloquentUser = EloquentUser::factory()->create();
        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $token = (new JwtTokenService())->generateToken($domainUser);

        $this->expectException(SignatureInvalidException::class);
        JWT::decode($token, new Key('a-different-secret-that-is-long-enough-32b', config('jwt.algo')));
    }
}
