<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123456'),
                'full_name' => 'Administrator',
                'phone' => '0987654321',
                'date_of_birth' => Carbon::create('1985', '05', '10'),
                'gender' => 'Male',
                'address' => 'Admin HQ',
                'user_type' => 'Admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'admin@bookmovie.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'Quản trị viên',
                'phone' => '0912345678',
                'date_of_birth' => Carbon::create('1990', '01', '01'),
                'gender' => 'Male',
                'address' => 'Việt Nam',
                'user_type' => 'Admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'customer@example.com',
                'password' => Hash::make('password123'),
                'full_name' => 'Khách Hàng Mẫu',
                'phone' => '0123456789',
                'date_of_birth' => Carbon::create('1995', '01', '01'),
                'gender' => 'Male',
                'address' => 'Hà Nội',
                'user_type' => 'Customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'staff@example.com',
                'password' => Hash::make('staffpassword'),
                'full_name' => 'Nhân Viên Rạp',
                'phone' => '0222333444',
                'date_of_birth' => Carbon::create('1995', '12', '25'),
                'gender' => 'Male',
                'address' => 'Hồ Chí Minh',
                'user_type' => 'Staff',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
