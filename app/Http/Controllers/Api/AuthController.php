<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //Register
 public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully. Please verify your email.',
                'data' => [
                    'user' => new UserResource($user),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

   //Login
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated());

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'data' => [
                    'email_verified' => false,
                ],
            ], 403);
        }

        // Create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 200);
    }

    //verify email address
    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail($request);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    //resend  email verify
    public function resendVerification(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->resendVerification($request);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend verification email.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


   //logout from your current device
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logoutCurrentDevice($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($request->user()->load('blogs')),
            ],
        ], 200);
    }
}
