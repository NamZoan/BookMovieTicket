<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Mail\PasswordResetOtpMail;
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

    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Email không tồn tại trong hệ thống.',
            ])->withInput();
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'password_reset_otp' => $otpCode,
            'password_reset_expires_at' => now()->addMinutes((int) config('auth.otp_expire', 10)),
        ])->save();

        Mail::to($user->email)->send(new PasswordResetOtpMail($otpCode));

        $request->session()->put('password_reset_email', $user->email);

        return redirect()
            ->route('auth.reset-password')
            ->with('status', 'Mã OTP đã được gửi. Vui lòng kiểm tra email.');
    }

    public function showResetPasswordForm(Request $request, ?string $token = null)
    {
        return view('client.auth.reset-password', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Email không tồn tại trong hệ thống.',
            ])->withInput();
        }

        if (! $user->password_reset_otp || ! $user->password_reset_expires_at) {
            return back()->withErrors([
                'otp' => 'Mã OTP không hợp lệ. Vui lòng yêu cầu lại.',
            ])->withInput();
        }

        if ($user->password_reset_expires_at->isPast()) {
            return back()->withErrors([
                'otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu lại.',
            ])->withInput();
        }

        if ($validated['otp'] !== $user->password_reset_otp) {
            return back()->withErrors([
                'otp' => 'Mã OTP không đúng.',
            ])->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'password_reset_otp' => null,
            'password_reset_expires_at' => null,
        ])->save();

        $request->session()->forget('password_reset_email');

        return redirect()
            ->route('auth.login')
            ->with('status', 'Mật khẩu đã được cập nhật. Vui lòng đăng nhập.');
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
