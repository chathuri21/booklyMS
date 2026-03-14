<?php

namespace App\Domain\Repositories;

use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function create(RegisterUserDTO $dto): User;
    public function findByEmail(string $email): ?User;
}