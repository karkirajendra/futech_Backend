<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{

    //register a new user
    public function register(array $data): User
{
    try {

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);



        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;

    } catch (\Exception $e) {


        Log::error('User registration failed', [
            'email'=> $data['email'],
            'error'=> $e->getMessage(),
        ]);
        throw $e;
    }
}

    //Email verification
      public function verifyEmail(EmailVerificationRequest $request){
            $request->fulfill();
             Log::info('Email verified', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
        ]);

        return [
            'message' => 'Email verified successfully',
        ];
        }

        //email resend verification
        public function resendVerification(Request $request): array
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified',
            ];
        }

        $user->sendEmailVerificationNotification();

        Log::info('Verification email resent', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return [
            'message' => 'Verification email resent',
        ];
    }



    //Login
    public function login(array $data): User|bool

    {
        $credentials=$data;
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Log::warning('Failed login attempt', [
                'email' => $credentials['email'],
            ]);

            return false;
        }

        Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;
    }
//Logout
    public function logout(User $user): void
    {
        // Revoke all tokens
        $user->tokens()->delete();

        Log::info('User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
