<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'email' => 'customer@example.com',
                'password' => bcrypt('password123'),
                'full_name' => 'John Doe',
                'phone' => '0123456789',
                'date_of_birth' => Carbon::create('1990', '01', '01'),
                'gender' => 'Male',
                'address' => '123 Main Street, City, Country',
                'loyalty_points' => 150,
                'user_type' => 'Customer',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'admin@example.com',
                'password' => bcrypt('adminpassword'),
                'full_name' => 'Admin User',
                'phone' => '0987654321',
                'date_of_birth' => Carbon::create('1985', '05', '10'),
                'gender' => 'Female',
                'address' => '456 Admin Ave, City, Country',
                'loyalty_points' => 500,
                'user_type' => 'Admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'staff@example.com',
                'password' => bcrypt('staffpassword'),
                'full_name' => 'Staff Member',
                'phone' => '0222333444',
                'date_of_birth' => Carbon::create('1995', '12', '25'),
                'gender' => 'Male',
                'address' => '789 Staff Road, City, Country',
                'loyalty_points' => 100,
                'user_type' => 'Staff',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
