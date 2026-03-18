<?php

namespace App\Domain\Entities;

use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Exceptions\InvalidCredentialsException;

use App\Models\User as EloquentUser;

class User
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        public string $phone,
        public string $password,
        public string $role,
        public bool $isActive
    ) {}

    public function checkPassword(string $password) : void 
    {
        if (!password_verify($password, $this->password)) {
            throw new InvalidCredentialsException();
        }
    }

    public function ensureIsActive(): void 
    {
        if (!$this->isActive) {
            throw new InactiveAccountException();
        }
    }
}