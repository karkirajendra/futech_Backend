<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed', // password_confirmation
        ]);

        $user = $this->authService->register($request->only('name','email','password'));

        return response()->json(['user'=>$user, 'message'=>'User registered successfully'],201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = $this->authService->login($request->only('email','password'));

        if (!$user) {
            return response()->json(['error'=>'Invalid credentials'],401);
        }

        return response()->json(['user'=>$user,'message'=>'Login successful']);
    }
}
