<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Kiểm tra email đã được xác thực chưa
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Vui lòng xác thực email của bạn trước khi đăng nhập. Chúng tôi đã gửi link xác thực đến email của bạn.'
                ])->withInput();
            }

            return redirect()->intended(route('client.home'));
        }

        return back()->withErrors(['email' => 'Thông tin đăng nhập không chính xác'])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'user_type' => 'Customer',
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('client.home');
    }
}
