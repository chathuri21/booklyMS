<?php

namespace Tests\Unit\Infrastructure\Persistence\EloquentUserRepositoryTest;
use App\Domain\DTOs\RegisterUserDTO;
use App\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_create_user(): void
    {
        $repository = new EloquentUserRepository();

        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $user = $repository->create($dto);

        $this->assertNotNull($user);
        $this->assertEquals($dto->name, $user->name);
        $this->assertEquals($dto->email, $user->email); 
        $this->assertEquals($dto->phone, $user->phone);
        $this->assertEquals($dto->role, $user->role);
    }
}