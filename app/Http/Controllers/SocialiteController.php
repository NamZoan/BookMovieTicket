<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        // yêu cầu email, profile từ Google
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Lấy user từ Google (id, name, email)
            $googleUser = Socialite::driver('google')->user();

            // Tìm theo provider_id trước
            $user = User::where('provider', 'google')
                        ->where('provider_id', $googleUser->getId())
                        ->first();

            // Nếu chưa liên kết provider, thử match theo email để tránh tạo trùng
            if (!$user && $googleUser->getEmail()) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            if (!$user) {
                $user = User::create([
                    'full_name'   => $googleUser->getName() ?? 'User',
                    'email'       => $googleUser->getEmail(), // bảng của bạn đang unique email
                    'password'    => null, // cho social login
                    'user_type'   => 'Customer',
                    'is_active'   => 1,
                    'provider'    => 'google',
                    'provider_id' => $googleUser->getId(),
                    'email_verified_at' => now(), // Google đã verify email
                ]);
            } else {
                // cập nhật thông tin mới nhất
                $user->update([
                    'provider'    => 'google',
                    'provider_id' => $user->provider_id ?: $googleUser->getId(),
                    'full_name'   => $user->full_name ?: ($googleUser->getName() ?? $user->full_name),
                    'email_verified_at' => $user->email_verified_at ?: now(), // Verify email nếu chưa verify
                ]);
            }

            Auth::login($user, remember: true);

            // Điều hướng về trang bạn muốn (vd: homepage, hoặc /)
            return redirect()->back();
        } catch (\Throwable $e) {
            // Ghi lại lỗi để dễ dàng debug
            \Illuminate\Support\Facades\Log::error('Google Login Failed: ' . $e->getMessage());
            return redirect()->route('auth.login')->with('error', 'Đăng nhập Google thất bại.');
        }
    }
}
