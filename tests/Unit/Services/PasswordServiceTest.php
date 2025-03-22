<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Services\Validation\PasswordService;
use Illuminate\Auth\AuthenticationException;

class PasswordServiceTest extends TestCase
{
    protected $passwordService;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordService = new PasswordService();
    }

    public function testValidPassword()
    {
        $plainPassword = 'password123';
        $hashedPassword = Hash::make($plainPassword);

        // Act: Validate the password, throws error when not matched
        $this->passwordService->validatePassword($plainPassword, $hashedPassword);

        // If no exception is thrown, the test passes
        $this->assertTrue(true);
    }

    public function testInvalidPassword()
    {
        $plainPassword = 'password123';
        $hashedPassword = Hash::make('wrongpassword');

        // Expect the exception to be thrown
        $this->expectException(AuthenticationException::class);

        // Act: Validate the password, throws error when not matched
        $this->passwordService->validatePassword($plainPassword, $hashedPassword);
    }
}
