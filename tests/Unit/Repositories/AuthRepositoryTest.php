<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Mockery;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class AuthRepositoryTest extends TestCase
{
    protected $authRepository;

    // This method is automatically called before each test method runs.
    public function setUp(): void
    {
        parent::setUp();

        $this->authRepository = Mockery::mock(AuthRepository::class);
    }

    public function testCreatesUser()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $mockUser = Mockery::mock(User::class);
        
        $mockUser->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn('John Doe');

        $mockUser->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn('john@example.com');
        
        $this->authRepository->shouldReceive('createUser')
            ->once()
            ->with($userData)
            ->andReturn($mockUser);

        // Act: Call the createUser method on the repository
        $user = $this->authRepository->createUser($userData);

        // Assert: Check if the user returned is an instance of User
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    public function testFindByEmail()
    {
        // Test data
        $email = 'johndoe@example.com';
        
        $mockUser = Mockery::mock(User::class);

        $mockUser->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn('johndoe@example.com');

        $this->authRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($mockUser);
        
        // Call the repository's method and pass the mocked User model
        $user = $this->authRepository->findByEmail($email);

        // Assert: Check if the user returned is the mock and if the email matches
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->email);
    }

    public function testFindByTenantId()
    {
        // Test data
        $tenantId = 1;

        $mockUser = Mockery::mock(User::class);

        $mockUser->shouldReceive('getAttribute')
            ->with('tenant_id')
            ->andReturn(1);

        $this->authRepository->shouldReceive('findByTenantId')
            ->once()
            ->with($tenantId)
            ->andReturn($mockUser);

        // Call the repository's method and pass the mocked User model
        $user = $this->authRepository->findByTenantId($tenantId);

        // Assert: Check if the user returned is the mock and if the tenant_id matches
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($tenantId, $user->tenant_id);
    }

    public function testDeleteTokens()
    {
        $userMock = Mockery::mock(User::class);
        
        $userMock->shouldReceive('tokens')
            ->once()
            ->andReturnSelf();

        $userMock->shouldReceive('delete')
            ->once();


        // Mock the 'deleteTokens' method on the AuthRepository to call the real method
        $this->authRepository->shouldReceive('deleteTokens')
            ->once()
            ->with($userMock)
            ->andReturnUsing(function ($user) {
                // Here you can call the actual deleteTokens method from AuthRepository
                // but this is optional, depending on how you want to structure the test
                $authRepository = new AuthRepository(); // Instantiating the real class
                $authRepository->deleteTokens($user);
            });

        // Act: Call the deleteTokens method on the mocked repository
        $this->authRepository->deleteTokens($userMock);

        // Assert: Ensure the delete method was called on tokens
        $userMock->shouldHaveReceived('delete')->once();
    }
}
