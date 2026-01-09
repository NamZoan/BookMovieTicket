<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('client.home'))->with('verified', true);
        }

        if (! $user->otp_code || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            $user->sendEmailVerificationNotification();
            $request->session()->flash('status', 'otp-sent');
        }

        return view('client.auth.verify-email', [
            'email' => $user->email,
        ]);
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('client.home'))->with('verified', true);
        }

        if (! $user->otp_code || ! $user->otp_expires_at) {
            return back()->withErrors([
                'otp' => 'Ma OTP khong hop le. Vui long gui lai ma.',
            ]);
        }

        if ($user->otp_expires_at->isPast()) {
            return back()->withErrors([
                'otp' => 'Ma OTP da het han. Vui long gui lai ma.',
            ]);
        }

        if (!$request->input('otp') == $user->otp_code) {
            return back()->withErrors([
                'otp' => 'Ma OTP khong dung.',
            ]);
        }

        if ($user->markEmailAsVerified()) {
            $user->forceFill([
                'otp_code' => null,
                'otp_expires_at' => null,
            ])->save();

            event(new Verified($user));
        }

        return redirect()->intended(route('client.home'))->with('verified', true);
    }

    /**
     * Send a new email verification notification.
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('client.home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'otp-sent');
    }
}
