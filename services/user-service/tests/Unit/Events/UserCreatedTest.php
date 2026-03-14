<?php

namespace Tests\Unit\Events;

use App\Domain\Entities\User;
use App\Events\UserCreated;
use PHPUnit\Framework\TestCase;

class UserCreatedTest extends TestCase
{
    public function test_user_created_event(): void
    {
        $user = new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: password_hash('password', PASSWORD_BCRYPT),
            role: 'customer',
            isActive: true,
            eloquentUser: null
        );

        $event = new UserCreated($user);

        $this->assertInstanceOf(UserCreated::class, $event);
        $this->assertSame($user, $event->user);
        $this->assertEquals('Test User', $event->user->name);
        $this->assertEquals('test@example.com', $event->user->email);
        $this->assertEquals('1234567890', $event->user->phone);
        $this->assertEquals('customer', $event->user->role);
    }
}