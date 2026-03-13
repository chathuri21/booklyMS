<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $is_active
    ) {}
}