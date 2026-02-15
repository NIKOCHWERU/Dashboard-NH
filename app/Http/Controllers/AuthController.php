<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/drive'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Role is 'user' by default via migration, admin can change it later
                ]
            );

            Auth::login($user);

            // Save the refresh token to a file if it exists
            // This allows the system to use THIS user's storage for all uploads
            if ($googleUser->refreshToken) {
                \Illuminate\Support\Facades\Storage::put('google-drive-token.json', json_encode([
                    'refresh_token' => $googleUser->refreshToken
                ]));
            }

            return redirect()->intended('/dashboard'); // Route to dashboard
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
