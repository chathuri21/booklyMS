<?php

namespace Tests\Unit\Domain\DTOs;

use App\Domain\DTOs\RegisterUserDTO;
use Tests\TestCase; 

class RegisterUserDTOTest extends TestCase
{
    public function test_from_request_creates_dto(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',    
            'password' => 'password',
            'role' => 'customer'
        ];

        $dto = RegisterUserDTO::fromRequest($data);

        $this->assertEquals($data['name'], $dto->name);
        $this->assertEquals($data['email'], $dto->email);
        $this->assertEquals($data['phone'], $dto->phone);
        $this->assertEquals($data['password'], $dto->password);
        $this->assertEquals($data['role'], $dto->role);
    }

    public function test_from_request_with_missing_fields(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ];

        $dto = RegisterUserDTO::fromRequest($data);

        $this->assertEquals(null, $dto->phone);
        $this->assertEquals('customer', $dto->role);
    }
}