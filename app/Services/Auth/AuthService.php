<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\TokenService;
use App\Services\Validation\PasswordService as PasswordValidationService;

class AuthService
{
    protected $authRepository;
    protected $tokenService;
    protected $passwordValidationService;

    public function __construct(AuthRepository $authRepository, 
                                TokenService $tokenService, 
                                PasswordValidationService $passwordValidationService)
    {
        $this->authRepository = $authRepository;
        $this->tokenService = $tokenService;
        $this->passwordValidationService = $passwordValidationService;
    }

    public function register(array $data)
    {
        $user = $this->authRepository->createUser($data);

        $token = $this->tokenService->generateToken($user);

        return [
            'message' => 'User registered successfully',
            'token' => $token,
        ];
    }

    public function login(array $credentials)
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials');
        }

        $this->passwordValidationService->validatePassword($credentials['password'], $user->password);
        
        $token = $this->tokenService->generateToken($user);

        return ['token' => $token];
    }

    public function logout($user)
    {
        $this->authRepository->deleteTokens($user);
        return ['message' => 'Logged out successfully'];
    }
}
