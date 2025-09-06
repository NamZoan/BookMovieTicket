<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $promotions = [
            [
                'code' => 'WELCOME10',
                'name' => 'Giảm 10% cho đơn đầu tiên',
                'description' => 'Giảm 10% cho khách hàng mới',
                'discount_type' => 'Percentage',
                'discount_value' => 10,
                'min_amount' => 100000,
                'max_discount' => 50000,
                'usage_limit' => 1000,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Miễn phí vận chuyển',
                'description' => 'Miễn phí vận chuyển cho đơn từ 300k',
                'discount_type' => 'Fixed Amount',
                'discount_value' => 30000,
                'min_amount' => 300000,
                'max_discount' => null,
                'usage_limit' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'is_active' => true
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}