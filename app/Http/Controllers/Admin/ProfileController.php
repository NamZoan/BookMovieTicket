<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function editPassword()
    {
        return view('admin.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed|different:current_password',
        ]);

        $user = $request->user();

        if (! $this->verifyPassword((string) $user->password, $request->input('current_password'))) {
            return back()->withErrors([
                'current_password' => 'Mat khau hien tai khong dung.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        return back()->with('success', 'Cap nhat mat khau thanh cong.');
    }

    private function verifyPassword(string $hash, string $plain): bool
    {
        $isBcrypt = preg_match('/^\\$2[abyx]\\$/', $hash) === 1;
        $isArgon = str_starts_with($hash, '$argon2i$') || str_starts_with($hash, '$argon2id$');

        if ($isBcrypt || $isArgon) {
            return password_verify($plain, $hash);
        }

        return hash_equals($hash, $plain) || hash_equals($hash, md5($plain));
    }
}
