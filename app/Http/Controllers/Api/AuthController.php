<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

//register
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                // 'regex:/[a-z]/',
                // 'regex:/[A-Z]/',
                // 'regex:/[0-9]/',
                // 'regex:/[@$!%*#?&]/',
            ],
        ]);

        $user = $this->authService->register($validated);
        $user->sendEmailVerificationNotification();

        return response()->json([
            'user' => $user,
            'message' => 'User registered successfully. Please verify your email.'
        ], 201);
    }

    //verify Email
    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->verifyEmail($request),
            200
        );
    }

   //resend email
    public function resendVerification(Request $request): JsonResponse
    {
        return response()->json(
            $this->authService->resendVerification($request),
            200
        );
    }

  //login user
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = $this->authService->login($validated);

        if (!$user) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Please verify your email address before logging in.'
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ], 200);
    }

   //logout user
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }


    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}
