<?php

namespace App\Infrastructure\Auth;

use App\Domain\Services\TokenServiceInterface;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenService implements TokenServiceInterface
{
    public function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}