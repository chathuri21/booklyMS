<?php

namespace App\Domain\Services;

use App\Models\User;

interface TokenServiceInterface
{
    public function generateToken(User$user): string;
}