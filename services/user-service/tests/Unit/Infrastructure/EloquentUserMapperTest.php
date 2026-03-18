<?php

namespace Tests\Unit\Infrastructure;

use App\Domain\Entities\User;
use App\Infrastructure\EloquentUserMapper;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EloquentUserMapperTest extends TestCase
{
    private function makeEloquentUser(int $isActive = 1) : EloquentUser {
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = 1;
        $eloquentUser->name = 'Test User';
        $eloquentUser->email = 'test@example.com';
        $eloquentUser->phone = '1234567890';
        $eloquentUser->password = 'password';
        $eloquentUser->role = 'customer';
        $eloquentUser->is_active = $isActive;

        return $eloquentUser;
    }

    public function test_to_domain_maps_correctly() : void
    {
        $eloquentUser = $this->makeEloquentUser();

        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $this->assertEquals(1, $domainUser->id);
        $this->assertEquals('Test User', $domainUser->name);
        $this->assertEquals('test@example.com', $domainUser->email);
        $this->assertEquals('1234567890', $domainUser->phone);
        $this->assertTrue(Hash::check('password', $domainUser->password));
        $this->assertEquals('customer', $domainUser->role);
        $this->assertTrue($domainUser->isActive);
        $this->assertInstanceOf(User::class, $domainUser);
    }

    public function test_to_entity_maps_correctly() : void
    {
        $domainUser = new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: 'password',
            role: 'customer',
            isActive: true
        );

        $eloquentUser = EloquentUserMapper::toEloquent($domainUser);

        $this->assertEquals(1, $eloquentUser->id);
        $this->assertEquals('Test User', $eloquentUser->name);
        $this->assertEquals('test@example.com', $eloquentUser->email);
        $this->assertEquals('1234567890', $eloquentUser->phone);
        $this->assertEquals('password', $domainUser->password);
        $this->assertEquals('customer', $eloquentUser->role);
        $this->assertTrue($eloquentUser->is_active);
        $this->assertInstanceOf(EloquentUser::class, $eloquentUser);
    }

    public function test_to_domain_casts_is_active_to_boolean(): void
    {
        $eloquentUser = $this->makeEloquentUser(0);

        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $this->assertFalse($domainUser->isActive);
    }
}