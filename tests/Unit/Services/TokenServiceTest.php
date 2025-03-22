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

    public function testGeneratesATokenFoUser()
    {
        // Arrangement
        $user = Mockery::mock(User::class)->makePartial();
        
        $tokenMock = Mockery::mock(\stdClass::class);
        $tokenMock->plainTextToken = 'fake_token';

        $user->shouldReceive('createToken')
            ->once()
            ->with('API Token')
            ->andReturn($tokenMock);
        
        // Action: Generate the token using TokenService
        $token = $this->tokenService->generateToken($user);

        // Assertion: The returned token should be the same as the fake token
        $this->assertEquals('fake_token', $token);
    }
}
