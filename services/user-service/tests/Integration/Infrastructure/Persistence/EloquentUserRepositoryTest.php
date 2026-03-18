<?php

namespace Tests\Integration\Infrastructure\Persistence\EloquentUserRepositoryTest;

use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Entities\User;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Models\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_create_user_with_hashed_password() : void
    {
        $repository = new EloquentUserRepository();

        $dto = new RegisterUserDTO(
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer'
        );

        $repository->create($dto);

        $eloquentUser = EloquentUser::where('email', 'test@example.com')->first();

        $this->assertTrue(Hash::check('password', $eloquentUser->password));
    }

    public function test_find_by_emai_returns_domain_user() : void
    {
        $eloquentUser = EloquentUser::factory()->create([
            'email' => 'test@example.com'
        ]);

        $repository = new EloquentUserRepository();

        $user = $repository->findByEmail('test@example.com');

        $this->assertNotNull($user);
        $this->assertEquals($eloquentUser->email, $user->email);
        $this->assertEquals($eloquentUser->name, $user->name);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_find_by_email_returns_null_when_user_not_found() : void
    {
        $repository = new EloquentUserRepository();

        $user = $repository->findByEmail('not-found@example.com');

        $this->assertNull($user);
    }

    public function test_get_model_by_id_returns_eloquent_user() : void
    {
        $eloquentUser = EloquentUser::factory()->create();

        $repository = new EloquentUserRepository();

        $user = $repository->getModelById($eloquentUser->id);

        $this->assertNotNull($user);
        $this->assertEquals($eloquentUser->id, $user->id);
        $this->assertInstanceOf(EloquentUser::class, $user);
    }

    public function test_get_model_by_id_returns_null_when_user_not_found() : void
    {
        $repository = new EloquentUserRepository();

        $user = $repository->getModelById(99999);

        $this->assertNull($user);
    }
}