<?php

namespace App\Infrastructure\Auth;

use App\Domain\Services\TokenServiceInterface;
use App\Domain\Entities\User;

class SanctumTokenService implements TokenServiceInterface
{
    public function generateToken(User $user): string
    {
        $eloquentUser = $user->getEloquentModel();
        return $eloquentUser->createToken('auth_token')->plainTextToken;
    }
}