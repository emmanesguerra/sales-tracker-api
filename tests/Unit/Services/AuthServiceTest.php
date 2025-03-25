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

    protected function mockUserAndTenant($userAttributes = [], $tenantAttributes = [])
    {
        $user = Mockery::mock(User::class);
        $tenant = Mockery::mock(Tenant::class);

        $tenant->shouldReceive('getAttribute')->with('id')->andReturn($tenantAttributes['id'] ?? 1);
        $tenant->shouldReceive('getAttribute')->with('subdomain')->andReturn($tenantAttributes['subdomain'] ?? 'tenant-subdomain');

        $user->shouldReceive('getAttribute')->with('tenant')->andReturn((object) ['subdomain' => $tenant->subdomain]);

        return [$user, $tenant];
    }

    public function testRegistersAUserAndReturnsASubdomain()
    {
        [$user, $tenant] = $this->mockUserAndTenant();

        // Mock interactions
        $this->authRepository->shouldReceive('createUser')->once()->andReturn($user);
        $this->tenantService->shouldReceive('generateSubdomain')->once()->with('John Doe')->andReturn('john-doe');
        $this->tenantRepository->shouldReceive('createTenant')->once()->andReturn($tenant);

        // Act: Call the register method
        $response = $this->authService->register([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Assert: Check the response
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('tenant_domain', $response);
        $this->assertEquals('User registered successfully', $response['message']);
        $this->assertEquals('tenant-subdomain', $response['tenant_domain']);
    }

    public function testLogsInAUserAndReturnsASubdomain()
    {
        $mockUser = Mockery::mock(User::class);
        $this->authRepository->shouldReceive('findByEmail')->once()->with('john@example.com')->andReturn($mockUser);

        $mockUser->shouldReceive('getAttribute')->with('password')->andReturn(Hash::make('password123'));
        $this->passwordValidationService->shouldReceive('validatePassword')->once()->with(Mockery::any(), $mockUser->password)->andReturn(true);
        
        $mockUser->shouldReceive('getAttribute')->with('tenant')->andReturn((object) ['subdomain' => 'tenant-subdomain']);

        // Act: Call the login method
        $response = $this->authService->login([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Assert: Check the response
        $this->assertArrayHasKey('tenant_domain', $response);
    }

    public function testRetriveToken()
    {
        // Arrangement
        $user = Mockery::mock(User::class);
        $tenant = Mockery::mock(Tenant::class);
        $this->authRepository->shouldReceive('findByTenantId')->once()->with(1)->andReturn($user);

        $this->tokenService->shouldReceive('generateToken')->once()->with($user)->andReturn('token');

        $user->shouldReceive('getAttribute')
            ->with('tenant')
            ->andReturnUsing(function() use ($tenant) {
                return $tenant;
            });

        $tenant->shouldReceive('getAttribute')->once()->with('subdomain')->andReturn('example-subdomain');
        $tenant->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);

        // Act: Call the retrieveToken method
        $response = $this->authService->retrieveToken(1);

        // Assert: Check the response
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('subdomain', $response);
        $this->assertArrayHasKey('tenant_id', $response);
    }

    public function testLogsOutAUserAndDeletesTokens()
    {
        // Arrangement
        $user = Mockery::mock(User::class);
        $this->authRepository->shouldReceive('deleteTokens')->once()->with($user)->andReturnNull();

        // Act: Call the logout method
        $response = $this->authService->logout($user);

        // Assert: Check the response
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Logged out successfully', $response['message']);
    }
}
