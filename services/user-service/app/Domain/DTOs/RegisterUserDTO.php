<?php

namespace App\Domain\DTOs;

class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $password,
        public string $role, // 'customer' or 'provider'
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? '',
            password: $data['password'],
            role: $data['role'] ?? 'customer',
        );
    }
}