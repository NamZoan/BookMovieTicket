<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            return redirect()->intended(route('client.home'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
    public function logout()
    {
        auth()->logout();
        return redirect()->route('client.home');
    }
}
