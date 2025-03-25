<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\TenantRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\TokenService;
use App\Services\Auth\TenantService;
use App\Services\Validation\PasswordService as PasswordValidationService;

class AuthService
{
    protected $authRepository;
    protected $tenantRepository;
    protected $tokenService;
    protected $tenantService;
    protected $passwordValidationService;

    public function __construct(AuthRepositoryInterface $authRepository, 
                                TenantRepositoryInterface $tenantRepository,
                                TokenService $tokenService, 
                                TenantService $tenantService,
                                PasswordValidationService $passwordValidationService)
    {
        $this->authRepository = $authRepository;
        $this->tenantRepository = $tenantRepository;
        $this->tokenService = $tokenService;
        $this->tenantService = $tenantService;
        $this->passwordValidationService = $passwordValidationService;
    }

    public function register(array $data)
    {
        $data['subdomain'] = $this->tenantService->generateSubdomain($data['name']);
        $tenant = $this->tenantRepository->createTenant($data);
        
        $data['tenant_id'] = $tenant->id;
        $user = $this->authRepository->createUser($data);

        return [
            'message' => 'User registered successfully',
            'tenant_domain' => $tenant->subdomain,
        ];
    }

    public function login(array $credentials)
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials');
        }

        $this->passwordValidationService->validatePassword($credentials['password'], $user->password);

        $tenant = $user->tenant;

        return [
            'tenant_domain' => $tenant->subdomain
        ];
    }

    public function retrieveToken(int $tenantId)
    {
        $user = $this->authRepository->findByTenantId($tenantId);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials');
        }
        
        $token = $this->tokenService->generateToken($user);

        return [
            'token' => $token,
            'subdomain' => $user->tenant->subdomain,
            'tenant_id' => $user->tenant->id,
        ];
    }

    public function logout($user)
    {
        $this->authRepository->deleteTokens($user);
        return ['message' => 'Logged out successfully'];
    }
}
