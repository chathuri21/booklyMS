<?php

namespace App\Domain\Repositories;

use App\Domain\DTOs\RegisterUserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterUserDTO $dto): User;
}