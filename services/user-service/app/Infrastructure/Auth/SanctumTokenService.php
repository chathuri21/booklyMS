<?php

namespace App\Infrastructure\Auth;

use App\Domain\Services\TokenServiceInterface;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as EloquetUser;

class SanctumTokenService implements TokenServiceInterface
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function generateToken(User $user): string
    {
        $eloquentUser = $this->userRepository->getModelById($user->id);
        return $eloquentUser->createToken('auth_token')->plainTextToken;
    }
}