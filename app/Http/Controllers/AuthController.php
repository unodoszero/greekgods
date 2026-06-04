<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Support\AuthSessionIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Masmerise\Toaster\Toaster;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = $request->user();

            if ($user instanceof User) {
                AuthSessionIdentity::store($request, $user);
            }
            Toaster::success('Logged in successfully.');

            return redirect()->intended('/profile');
        }

        Toaster::error('Invalid email or password.');

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $user = User::create($request->userAttributes());

        Auth::login($user);
        $request->session()->regenerate();
        AuthSessionIdentity::store($request, $user);
        Toaster::success('Account created successfully.');

        return redirect('/profile');
    }

    public function logout(Request $request): RedirectResponse
    {
        AuthSessionIdentity::clear($request);

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::guard()->forgetUser();
        Toaster::info('Logged out successfully.');

        return redirect('/login');
    }
}
