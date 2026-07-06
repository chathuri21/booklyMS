<?php

namespace App\Infrastructure\Auth;

use App\Domain\Entities\User;
use App\Domain\Services\TokenServiceInterface;
use Firebase\JWT\JWT;

class JwtTokenService implements TokenServiceInterface
{
    public function generateToken(User $user): string
    {
        $now = time();

        $claims = [
            'iss' => config('jwt.issuer'),
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => $now,
            'exp' => $now + config('jwt.ttl') * 60,
        ];

        return JWT::encode($claims, config('jwt.secret'), config('jwt.algo'));
    }
}
