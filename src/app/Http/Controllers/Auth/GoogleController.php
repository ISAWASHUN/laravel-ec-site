<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->email,
                ],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]
            );

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate user'], 500);
        }
    }
}
