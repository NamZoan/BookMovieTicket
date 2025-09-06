<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pricing;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pricings = [
            // Regular seats
            ['seat_type' => 'Regular', 'day_type' => 'Weekday', 'time_slot' => 'Morning', 'price_multiplier' => 0.8],
            ['seat_type' => 'Regular', 'day_type' => 'Weekday', 'time_slot' => 'Afternoon', 'price_multiplier' => 1.0],
            ['seat_type' => 'Regular', 'day_type' => 'Weekday', 'time_slot' => 'Evening', 'price_multiplier' => 1.2],
            ['seat_type' => 'Regular', 'day_type' => 'Weekday', 'time_slot' => 'Late Night', 'price_multiplier' => 0.6],
            
            ['seat_type' => 'Regular', 'day_type' => 'Weekend', 'time_slot' => 'Morning', 'price_multiplier' => 1.0],
            ['seat_type' => 'Regular', 'day_type' => 'Weekend', 'time_slot' => 'Afternoon', 'price_multiplier' => 1.3],
            ['seat_type' => 'Regular', 'day_type' => 'Weekend', 'time_slot' => 'Evening', 'price_multiplier' => 1.5],
            ['seat_type' => 'Regular', 'day_type' => 'Weekend', 'time_slot' => 'Late Night', 'price_multiplier' => 0.8],
            
            ['seat_type' => 'Regular', 'day_type' => 'Holiday', 'time_slot' => 'Morning', 'price_multiplier' => 1.2],
            ['seat_type' => 'Regular', 'day_type' => 'Holiday', 'time_slot' => 'Afternoon', 'price_multiplier' => 1.5],
            ['seat_type' => 'Regular', 'day_type' => 'Holiday', 'time_slot' => 'Evening', 'price_multiplier' => 1.8],
            ['seat_type' => 'Regular', 'day_type' => 'Holiday', 'time_slot' => 'Late Night', 'price_multiplier' => 1.0],

            // VIP seats
            ['seat_type' => 'VIP', 'day_type' => 'Weekday', 'time_slot' => 'Morning', 'price_multiplier' => 1.5],
            ['seat_type' => 'VIP', 'day_type' => 'Weekday', 'time_slot' => 'Afternoon', 'price_multiplier' => 1.8],
            ['seat_type' => 'VIP', 'day_type' => 'Weekday', 'time_slot' => 'Evening', 'price_multiplier' => 2.0],
            ['seat_type' => 'VIP', 'day_type' => 'Weekday', 'time_slot' => 'Late Night', 'price_multiplier' => 1.2],
            
            ['seat_type' => 'VIP', 'day_type' => 'Weekend', 'time_slot' => 'Morning', 'price_multiplier' => 1.8],
            ['seat_type' => 'VIP', 'day_type' => 'Weekend', 'time_slot' => 'Afternoon', 'price_multiplier' => 2.2],
            ['seat_type' => 'VIP', 'day_type' => 'Weekend', 'time_slot' => 'Evening', 'price_multiplier' => 2.5],
            ['seat_type' => 'VIP', 'day_type' => 'Weekend', 'time_slot' => 'Late Night', 'price_multiplier' => 1.5],
            
            ['seat_type' => 'VIP', 'day_type' => 'Holiday', 'time_slot' => 'Morning', 'price_multiplier' => 2.0],
            ['seat_type' => 'VIP', 'day_type' => 'Holiday', 'time_slot' => 'Afternoon', 'price_multiplier' => 2.5],
            ['seat_type' => 'VIP', 'day_type' => 'Holiday', 'time_slot' => 'Evening', 'price_multiplier' => 3.0],
            ['seat_type' => 'VIP', 'day_type' => 'Holiday', 'time_slot' => 'Late Night', 'price_multiplier' => 1.8],

            // Couple seats
            ['seat_type' => 'Couple', 'day_type' => 'Weekday', 'time_slot' => 'Morning', 'price_multiplier' => 1.8],
            ['seat_type' => 'Couple', 'day_type' => 'Weekday', 'time_slot' => 'Afternoon', 'price_multiplier' => 2.0],
            ['seat_type' => 'Couple', 'day_type' => 'Weekday', 'time_slot' => 'Evening', 'price_multiplier' => 2.3],
            ['seat_type' => 'Couple', 'day_type' => 'Weekday', 'time_slot' => 'Late Night', 'price_multiplier' => 1.5],
            
            ['seat_type' => 'Couple', 'day_type' => 'Weekend', 'time_slot' => 'Morning', 'price_multiplier' => 2.0],
            ['seat_type' => 'Couple', 'day_type' => 'Weekend', 'time_slot' => 'Afternoon', 'price_multiplier' => 2.5],
            ['seat_type' => 'Couple', 'day_type' => 'Weekend', 'time_slot' => 'Evening', 'price_multiplier' => 3.0],
            ['seat_type' => 'Couple', 'day_type' => 'Weekend', 'time_slot' => 'Late Night', 'price_multiplier' => 1.8],
            
            ['seat_type' => 'Couple', 'day_type' => 'Holiday', 'time_slot' => 'Morning', 'price_multiplier' => 2.5],
            ['seat_type' => 'Couple', 'day_type' => 'Holiday', 'time_slot' => 'Afternoon', 'price_multiplier' => 3.0],
            ['seat_type' => 'Couple', 'day_type' => 'Holiday', 'time_slot' => 'Evening', 'price_multiplier' => 3.5],
            ['seat_type' => 'Couple', 'day_type' => 'Holiday', 'time_slot' => 'Late Night', 'price_multiplier' => 2.0],
        ];

        foreach ($pricings as $pricing) {
            Pricing::create($pricing);
        }
    }
}

