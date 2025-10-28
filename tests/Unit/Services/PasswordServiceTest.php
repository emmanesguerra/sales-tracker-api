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

    public function testValidPasswordComparison()
    {
        $plainPassword = 'password123';
        $hashedPassword = Hash::make($plainPassword);

        // Act: Validate the password
        $this->passwordService->validatePassword($plainPassword, $hashedPassword);

        // Assert: No exception is thrown, hence password is valid
        $this->assertTrue(true);
    }

    public function testInvalidPasswordComparison()
    {
        $plainPassword = 'password123';
        $hashedPassword = Hash::make('wrongpassword');

        // Expect the exception to be thrown
        $this->expectException(AuthenticationException::class);

        // Act: Validate the password and expect failure
        $this->passwordService->validatePassword($plainPassword, $hashedPassword);
    }
}
