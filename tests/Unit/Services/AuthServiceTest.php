<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Auth\AuthService;
use App\Repositories\Auth\AuthRepositoryInterface as AuthRepository;
use App\Repositories\Auth\TenantRepositoryInterface as TenantRepository;
use App\Services\Auth\TokenService;
use App\Services\Auth\TenantService;
use App\Services\Validation\PasswordService;
use Mockery;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;

class AuthServiceTest extends TestCase
{
    protected $authRepository;
    protected $tenantRepository;
    protected $tokenService;
    protected $tenantService;
    protected $passwordValidationService;
    protected $authService;

    public function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->authRepository = Mockery::mock(AuthRepository::class);
        $this->tenantRepository = Mockery::mock(TenantRepository::class);
        $this->tokenService = Mockery::mock(TokenService::class);
        $this->tenantService = Mockery::mock(TenantService::class);
        $this->passwordValidationService = Mockery::mock(PasswordService::class);

        // Instantiate AuthService with mocked dependencies
        $this->authService = new AuthService(
            $this->authRepository,
            $this->tenantRepository,
            $this->tokenService,
            $this->tenantService,
            $this->passwordValidationService
        );
    }

    public function testRegistersAUserAndReturnsAToken()
    {
        // Arrangements
        $user = Mockery::mock(User::class);
        $tenant = Mockery::mock(Tenant::class);

        $tenant->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $tenant->shouldReceive('getAttribute')
            ->with('subdomain')
            ->andReturn('tenant-subdomain'); 
        
        $this->authRepository->shouldReceive('createUser')
            ->once()
            ->andReturn($user);
        
        $this->tenantService->shouldReceive('generateSubdomain')
            ->once()
            ->with('John Doe')
            ->andReturn('john-doe');
        
        $this->tenantRepository->shouldReceive('createTenant')
            ->once()
            ->andReturn($tenant);
        
        // Mocking the generateToken method to return a token string
        $this->tokenService->shouldReceive('generateToken')
            ->once()
            ->with($user)
            ->andReturn('fake_token');
    
        // Act: Call the register method
        $response = $this->authService->register([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
    
        // Assert: Check the response
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('token', $response);
        $this->assertEquals('User registered successfully', $response['message']);
        $this->assertEquals('fake_token', $response['token']);
        $this->assertEquals('tenant-subdomain', $response['tenant_domain']); // Assert the correct subdomain
    }

    public function testLogsInAUserAndReturnsAToken()
    {
        // Arrangements
        $user = new User();
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->password = '$2y$10$eUjBrfNArCm9JT6M5Z.wKW8s0NZM7XxhgwwtLZzUme6KhV0nA2'; // Example hashed password

        $this->authRepository->shouldReceive('findByEmail')
            ->once()
            ->with('john@example.com')
            ->andReturn($user);

        $this->passwordValidationService->shouldReceive('validatePassword')
            ->once()
            ->with(Mockery::any(), $user->password)
            ->andReturn(true);

        $this->tokenService->shouldReceive('generateToken')
            ->once()
            ->with($user)
            ->andReturn('fake_token');

        // Act: Call the login method
        $response = $this->authService->login([
            'email' => 'john@example.com',
            'password' => 'password123',  // Correct password
        ]);

        // Assert: Check the response
        $this->assertArrayHasKey('token', $response);
        $this->assertEquals('fake_token', $response['token']);
    }

    public function testLogsOutAUserAndDeletesTokens()
    {
        // Arrangement
        $user = Mockery::mock(User::class);
        $this->authRepository->shouldReceive('deleteTokens')
            ->once()
            ->with($user)
            ->andReturnNull();

        // Act: Call the logout method
        $response = $this->authService->logout($user);

        // Assert: Check the response
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Logged out successfully', $response['message']);
    }
}
