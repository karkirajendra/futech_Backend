<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class AuthService
{

    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

    }
      public function verifyEmail(EmailVerificationRequest $request){
            $request->fulfill();
            return['message'=>'Email verified successfully'];
        }

        public function resendVerification(Request $request){
            $user = $request->user();

            if ($user->hasVerifiedEmail()){
                return ['message'=>'Email already Verified'];
            }
                $user->sendEmailVerificationNotification();
                return ['message'=> 'Verification Email resent'];

        }



    public function login(array $data): User|bool
    {
        $user = User::where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            return $user;
        }

        return false;
    }
}
