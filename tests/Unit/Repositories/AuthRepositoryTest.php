<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Mockery;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;

class AuthRepositoryTest extends TestCase
{
    protected $authRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->authRepository = Mockery::mock(AuthRepository::class);
    }

    protected function mockUser($attributes = [])
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->andReturnUsing(function ($attribute) use ($attributes) {
            return $attributes[$attribute] ?? null;
        });
        return $user;
    }

    public function testCreatesUser()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $mockUser = $this->mockUser([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        $this->authRepository->shouldReceive('createUser')
            ->once()
            ->with($userData)
            ->andReturn($mockUser);

        $user = $this->authRepository->createUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('johndoe@example.com', $user->email);
    }

    public function testFindByEmail()
    {
        $email = 'johndoe@example.com';
        $mockUser = $this->mockUser(['email' => $email]);

        $this->authRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($mockUser);

        $user = $this->authRepository->findByEmail($email);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->email);
    }

    public function testFindByTenantId()
    {
        $tenantId = 1;
        $mockUser = $this->mockUser(['tenant_id' => $tenantId]);

        $this->authRepository->shouldReceive('findByTenantId')
            ->once()
            ->with($tenantId)
            ->andReturn($mockUser);

        $user = $this->authRepository->findByTenantId($tenantId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($tenantId, $user->tenant_id);
    }

    public function testDeleteTokens()
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('tokens')->once()->andReturnSelf();
        $userMock->shouldReceive('delete')->once();

        $this->authRepository->shouldReceive('deleteTokens')
            ->once()
            ->with($userMock)
            ->andReturnUsing(function ($user) {
                $authRepository = new AuthRepository();
                $authRepository->deleteTokens($user);
            });

        $this->authRepository->deleteTokens($userMock);

        $userMock->shouldHaveReceived('delete')->once();
    }
}
