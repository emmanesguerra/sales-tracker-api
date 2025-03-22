<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Mockery;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authServiceMock;
    protected $authController;

    public function setUp(): void
    {
        parent::setUp();

        // Mock AuthService
        $this->authServiceMock = Mockery::mock(AuthService::class);

        // Bind the mock to the container for dependency injection
        $this->app->instance(AuthService::class, $this->authServiceMock);

        // Create an instance of the AuthController
        $this->authController = new AuthController($this->authServiceMock);
    }

    public function testRegister()
    {
        // Prepare the mock return value
        $validatedData = ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password123'];
        $this->authServiceMock->shouldReceive('register')
            ->once()
            ->with($validatedData)
            ->andReturn(['token' => 'fake-token']); // Simulating the response from the service

        // Simulate a request to the register route
        $response = $this->postJson('/api/register', $validatedData);

        // Assert that the status code is 201 (created)
        $response->assertStatus(201);

        // Assert that the response contains the correct data
        $response->assertJson([
            'token' => 'fake-token',
        ]);
    }

    public function testLogin()
    {
        // Create a user to simulate login
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Set up the mock to return a response when 'login' is called
        $this->authServiceMock->shouldReceive('login')
            ->once()
            ->with([
                'email' => 'johndoe@example.com',
                'password' => 'password123',
            ])
            ->andReturn(['token' => 'fake-token']);

        // Act: Make the request to the login route
        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ]);

        // Assert: Check if the response status is 200 and contains the expected token
        $response->assertStatus(200)
                 ->assertJson([
                     'token' => 'fake-token',
                 ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $this->authServiceMock->shouldReceive('logout')
            ->once()
            ->andReturn(['message' => 'Logged out successfully']);

        // Act: Make the logout request with the valid token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        // Assert: Check if the response status is 200 and the message is correct
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out successfully']);
    }

    public function testLoginValidation()
    {
        // Test invalid email
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);
        $response->assertStatus(422); // Unprocessable Entity status code
        $response->assertJsonValidationErrors('email'); // Assert email validation error

        // Test missing email
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email'); // Assert email validation error

        // Test missing password
        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password'); // Assert password validation error
    }

    public function testInvalidRoute()
    {
        // Sending a GET request to a non-existent route
        $response = $this->getJson('/api/logins');
        
        $response->assertStatus(404);
    }

    public function testInvalidMethod()
    {
        // Test a POST request to a route that only allows GET (for example)
        $response = $this->getJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(405);

        // Test GET request for a route that expects POST (example)
        $response = $this->getJson('/api/register');
        $response->assertStatus(405);
    }
}
