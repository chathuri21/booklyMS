<?php

namespace Tests\Unit\Domain\Entities; 

use App\Domain\Entities\User;
use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Exceptions\InvalidCredentialsException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function makeUser(bool $isActive = true): User
    {
        return new User(
            id: 1,
            name: 'Test User',
            email: 'test@example.com',
            phone: '1234567890',
            password: password_hash('password', PASSWORD_BCRYPT),
            role: 'customer',
            isActive: $isActive
        );
    }

    public function test_check_password_for_correct_password() : void
    {
        $user = $this->makeUser();

        $user->checkPassword('password');
        $this->assertTrue(true);
    }

    public function test_check_password_for_wrong_password() : void
    {
        $this->expectException(InvalidCredentialsException::class);

        $user = $this->makeUser();

        $user->checkPassword('wrong-password');
    }

    public function test_ensure_is_active_for_active_user() : void
    {
        $user = $this->makeUser();

        $user->ensureIsActive();
        $this->assertTrue(true);
    }

    public function test_ensure_is_active_for_inactive_user() : void
    {
        $this->expectException(InactiveAccountException::class);

        $user = $this->makeUser(false);

        $user->ensureIsActive();
    }
}