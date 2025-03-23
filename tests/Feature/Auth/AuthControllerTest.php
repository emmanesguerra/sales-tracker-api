<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Mockery;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Tenant;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authServiceMock;
    protected $authController;

    public function setUp(): void
    {
        parent::setUp();

        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->app->instance(AuthService::class, $this->authServiceMock);

        $this->authController = new AuthController($this->authServiceMock);

        $this->withoutMiddleware('tenant');
    }

    protected function createTenantAndUser($email = 'johndoe@example.com', $password = 'password123')
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        return [$tenant, $user];
    }

    protected function createTokenForUser(User $user)
    {
        return $user->createToken('TestToken')->plainTextToken;
    }

    public function testRegister()
    {
        $this->authServiceMock->shouldReceive('register')
            ->once()
            ->with([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123'
            ])
            ->andReturn(['token' => 'fake-token']);

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201)->assertJson(['token' => 'fake-token']);
    }

    public function testLogin()
    {
        list($tenant, $user) = $this->createTenantAndUser();

        $this->authServiceMock->shouldReceive('login')
            ->once()
            ->with([
                'email' => 'johndoe@example.com',
                'password' => 'password123',
            ])
            ->andReturn(['token' => 'fake-token']);

        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)->assertJson(['token' => 'fake-token']);
    }

    public function testRetrieveToken()
    {
        list($tenant, $user) = $this->createTenantAndUser();
        
        $this->authServiceMock->shouldReceive('retrieveToken')
            ->once()
            ->with($tenant->id)
            ->andReturn(['token' => 'fake-token']);

        $response = $this->getJson("http://{$tenant->subdomain}." . env('APP_DOMAIN') . '/api/retrieve-token');
        $response->assertStatus(200)->assertJson(['token' => 'fake-token']);
    }

    public function testLogout()
    {
        list($tenant, $user) = $this->createTenantAndUser();

        $token = $this->createTokenForUser($user);

        $this->authServiceMock->shouldReceive('logout')
            ->once()
            ->andReturn(['message' => 'Logged out successfully']);

        $response = $this->withHeaders([
                'Authorization' => "Bearer {$token}",
                'Host' => "{$tenant->subdomain}.localhost",
            ])
            ->post("http://{$tenant->subdomain}." . env('APP_DOMAIN') . '/api/logout');

        $response->assertStatus(200)->assertJson(['message' => 'Logged out successfully']);
    }

    public function testLoginValidation()
    {
        $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');

        $this->postJson('/api/login', [
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');

        $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    }

    public function testInvalidRoute()
    {
        $this->getJson('/api/logins')->assertStatus(404);
    }

    public function testInvalidMethod()
    {
        $this->getJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ])->assertStatus(405);

        $this->getJson('/api/register')->assertStatus(405);
    }
}
