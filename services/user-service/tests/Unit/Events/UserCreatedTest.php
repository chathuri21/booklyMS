<?php

namespace Tests\Unit\Events;
use App\Events\UserCreated;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserCreatedTest extends TestCase
{
    public function test_user_created_event(): void
    {
        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'role' => 'customer',
        ]);

        $event = new UserCreated($user);

        $this->assertInstanceOf(UserCreated::class, $event);
        $this->assertSame($user, $event->user);
        $this->assertEquals('Test User', $event->user->name);
        $this->assertEquals('test@example.com', $event->user->email);
        $this->assertEquals('1234567890', $event->user->phone);
        $this->assertEquals('customer', $event->user->role);
    }
}