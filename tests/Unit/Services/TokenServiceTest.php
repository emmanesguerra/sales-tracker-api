<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Auth\TokenService;
use Tests\TestCase;
use Mockery;

class TokenServiceTest extends TestCase
{
    protected $tokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new TokenService();
    }

    public function testGeneratesATokenForUser()
    {
        // Arrange: Create a mock User object
        $user = Mockery::mock(User::class)->makePartial();

        // Mock the token response
        $tokenMock = Mockery::mock(\stdClass::class);
        $tokenMock->plainTextToken = 'fake_token';

        // Expect the createToken method to be called once
        $user->shouldReceive('createToken')
            ->once()
            ->with('API Token')
            ->andReturn($tokenMock);

        // Act: Generate the token using TokenService
        $token = $this->tokenService->generateToken($user);

        // Assert: The returned token should match the fake token
        $this->assertEquals('fake_token', $token);
    }

    public function tearDown(): void
    {
        // Clean up Mockery
        Mockery::close();
        parent::tearDown();
    }
}
