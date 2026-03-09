<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_creates_user(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
    }

    public function test_user_has_appointments_as_customer_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(method_exists($user, 'appointmentsAsCustomer'));
    }

    public function test_password_is_hashed() : void
    {
        $user = User::factory()->create([
            'password' => 'plaintextpassword',
        ]);

        $this->assertNotEquals('plaintextpassword', $user->password);
        $this->assertTrue(password_verify('plaintextpassword', $user->password));
    }
}