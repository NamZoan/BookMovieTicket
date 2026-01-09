<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Carbon;
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

        $remember = $request->boolean('remember');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $this->verifyPassword($user, $credentials['password'])) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            if (! $user->hasVerifiedEmail()) {
                $redirectTarget = $request->input('redirect_to', route('client.home'));
                $request->session()->put('url.intended', $redirectTarget);

                if (! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
                    $user->sendEmailVerificationNotification();

                    return redirect()
                        ->route('verification.notice')
                        ->with('status', 'otp-sent');
                }

                return redirect()
                    ->route('verification.notice')
                    ->with('status', 'otp-required');
            }

            return redirect()->intended(
                $request->input('redirect_to', route('client.home'))
            );
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác',
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
        $otpCode = sprintf('%06d', random_int(0, 999999));

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'user_type' => 'Customer',
            'is_active' => true,
            'email_verified_at' => null,
            'otp_code' => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(3),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($otpCode));        
        Auth::login($user);

        $redirectTarget = $request->input('redirect_to', route('client.home'));
        $request->session()->put('url.intended', $redirectTarget);

        return redirect()
            ->route('verification.notice')
            ->with('status', 'otp-sent');
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

    private function verifyPassword(User $user, string $plain): bool
    {
        $hash = (string) $user->password;
        $isBcrypt = preg_match('/^\\$2[abyx]\\$/', $hash) === 1;
        $isArgon = str_starts_with($hash, '$argon2i$') || str_starts_with($hash, '$argon2id$');

        if ($isBcrypt || $isArgon) {
            if (! password_verify($plain, $hash)) {
                return false;
            }

            if ($isArgon || Hash::needsRehash($hash)) {
                $user->forceFill(['password' => Hash::make($plain)])->save();
            }

            return true;
        }

        if (hash_equals($hash, $plain) || hash_equals($hash, md5($plain))) {
            $user->forceFill(['password' => Hash::make($plain)])->save();
            return true;
        }

        return false;
    }
}
