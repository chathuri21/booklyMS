<?php

namespace App\Domain\Entities;

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
        public bool $isActive,
        private ?EloquentUser $eloquentUser = null
    ) {}

    public function getEloquentModel(): EloquentUser
    {
        return $this->eloquentUser;
    }
}