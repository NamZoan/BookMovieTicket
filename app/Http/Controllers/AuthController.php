<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $redirectTo = $this->resolveRedirectTarget($request);

        return view('client.auth.login', compact('redirectTo'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(
                $request->input('redirect_to', route('client.home'))
            );
        }

        return back()->withErrors([
            'email' => 'Thong tin dang nhap khong chinh xac',
        ])->withInput();
    }

    public function showRegistrationForm(Request $request)
    {
        $redirectTo = $this->resolveRedirectTarget($request);

        return view('client.auth.register', compact('redirectTo'));
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
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->to(
            $request->input('redirect_to', route('client.home'))
        );
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('client.home');
    }

    private function resolveRedirectTarget(Request $request): string
    {
        $fallback = route('client.home');
        $previous = url()->previous();

        if (!$previous || $previous === $request->fullUrl()) {
            return $fallback;
        }

        // Avoid looping back to auth pages
        if (str_contains($previous, '/auth/login') || str_contains($previous, '/auth/register')) {
            return $fallback;
        }

        return $previous;
    }
}
