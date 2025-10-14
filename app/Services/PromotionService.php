<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Kiểm tra và áp dụng mã giảm giá
     */
    public function validateAndApplyPromotion($promotionCode, $totalAmount, $userId = null)
    {
        // Kiểm tra mã WELCOME50K cho người mới
        if ($promotionCode === 'WELCOME50K') {
            return $this->validateWelcomeOffer($userId, $totalAmount);
        }

        // Kiểm tra mã promotion thông thường
        $promotion = Promotion::where('code', $promotionCode)
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('usage_limit > used_count');
            })
            ->first();

        if (!$promotion) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'
            ];
        }

        // Kiểm tra điều kiện tối thiểu
        if ($totalAmount < $promotion->min_amount) {
            return [
                'success' => false,
                'message' => "Đơn hàng phải tối thiểu " . number_format((float)$promotion->min_amount) . "đ để sử dụng mã này."
            ];
        }

        // Kiểm tra người dùng đã sử dụng mã này chưa (nếu có giới hạn)
        if ($promotion->usage_limit && $userId) {
            $userUsageCount = Booking::where('user_id', $userId)
                ->where('promotion_code', $promotionCode)
                ->where('payment_status', 'Paid')
                ->count();

            if ($userUsageCount >= $promotion->usage_limit) {
                return [
                    'success' => false,
                    'message' => 'Bạn đã sử dụng hết lượt cho mã giảm giá này.'
                ];
            }
        }

        // Tính toán giảm giá
        $discountAmount = $this->calculateDiscount($promotion, $totalAmount);

        return [
            'success' => true,
            'promotion' => $promotion,
            'discount_amount' => $discountAmount,
            'message' => 'Áp dụng mã giảm giá thành công!'
        ];
    }

    /**
     * Kiểm tra ưu đãi người mới WELCOME50K
     */
    public function validateWelcomeOffer($userId, $totalAmount)
    {
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng ưu đãi người mới.'
            ];
        }

        // Kiểm tra người dùng có phải người mới không (chưa có booking thành công nào)
        $hasSuccessfulBooking = Booking::where('user_id', $userId)
            ->where('payment_status', 'Paid')
            ->exists();

        if ($hasSuccessfulBooking) {
            return [
                'success' => false,
                'message' => 'Ưu đãi người mới chỉ áp dụng cho khách hàng lần đầu.'
            ];
        }

        // Kiểm tra đã sử dụng WELCOME50K chưa
        $hasUsedWelcome = Booking::where('user_id', $userId)
            ->where('promotion_code', 'WELCOME50K')
            ->where('payment_status', 'Paid')
            ->exists();

        if ($hasUsedWelcome) {
            return [
                'success' => false,
                'message' => 'Bạn đã sử dụng ưu đãi người mới rồi.'
            ];
        }

        $discountAmount = min(50000, $totalAmount); // Tối đa 50k

        return [
            'success' => true,
            'promotion' => (object) [
                'code' => 'WELCOME50K',
                'name' => 'Ưu đãi người mới',
                'description' => 'Giảm 50.000đ cho khách hàng lần đầu',
                'discount_type' => 'Fixed Amount',
                'discount_value' => 50000
            ],
            'discount_amount' => $discountAmount,
            'message' => 'Chúc mừng! Bạn được giảm 50.000đ từ ưu đãi người mới.'
        ];
    }

    /**
     * Tính toán số tiền giảm giá
     */
    public function calculateDiscount($promotion, $totalAmount)
    {
        if ($promotion->discount_type === 'Percentage') {
            $discount = ($totalAmount * $promotion->discount_value) / 100;

            if ($promotion->max_discount && $discount > $promotion->max_discount) {
                $discount = $promotion->max_discount;
            }
        } else {
            $discount = $promotion->discount_value;
        }

        return min($discount, $totalAmount); // Không được giảm quá tổng tiền
    }

    /**
     * Lấy danh sách mã giảm giá khả dụng cho người dùng
     */
    public function getAvailablePromotions($userId = null, $totalAmount = 0)
    {
        // Use a Collection to avoid accidentally calling collection methods on arrays
        $promotions = collect();

        // Kiểm tra ưu đãi người mới
        if ($userId && $totalAmount >= 0) {
            $welcomeResult = $this->validateWelcomeOffer($userId, $totalAmount);
            if ($welcomeResult['success']) {
                $promotions->push([
                    'code' => 'WELCOME50K',
                    'name' => 'Ưu đãi người mới',
                    'description' => 'Giảm 50.000đ cho khách hàng lần đầu',
                    'discount_type' => 'Fixed Amount',
                    'discount_value' => 50000,
                    'min_amount' => 0,
                    'is_auto_applicable' => true
                ]);
            }
        }

        // Lấy các mã promotion khác
        $regularPromotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('usage_limit > used_count');
            })
            ->where('min_amount', '<=', $totalAmount)
            ->orderBy('discount_value', 'desc')
            ->get()
            ->map(function ($promotion) {
                return [
                    'code' => $promotion->code,
                    'name' => $promotion->name,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'min_amount' => $promotion->min_amount,
                    'max_discount' => $promotion->max_discount,
                    'is_auto_applicable' => false
                ];
            });

        // $regularPromotions is a Collection; merge with our collection and return array
        return $promotions->merge($regularPromotions)->values()->all();
    }

    /**
     * Kiểm tra xung đột giữa các mã giảm giá
     */
    public function checkPromotionConflict($promotionCode1, $promotionCode2)
    {
        // WELCOME50K không thể kết hợp với mã khác
        if (($promotionCode1 === 'WELCOME50K' && $promotionCode2 !== 'WELCOME50K') ||
            ($promotionCode2 === 'WELCOME50K' && $promotionCode1 !== 'WELCOME50K')) {
            return true;
        }

        return false;
    }

    /**
     * Cập nhật số lần sử dụng promotion
     */
    public function incrementPromotionUsage($promotionCode)
    {
        if ($promotionCode === 'WELCOME50K') {
            // WELCOME50K không có trong bảng promotions
            return true;
        }

        return Promotion::where('code', $promotionCode)->increment('used_count');
    }
}
