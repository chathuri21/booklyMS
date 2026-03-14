<?php

namespace App\Domain\Services;

use App\Domain\Entities\User;

interface TokenServiceInterface
{
    public function generateToken(User $user): string;
}