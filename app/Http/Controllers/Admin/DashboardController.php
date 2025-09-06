<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;
use App\Models\Cinema;

class DashboardController extends Controller
{
    public function index()
    {
        // $stats = [
        //     'total_movies' => Movie::count(),
        //     'total_bookings' => Booking::count(),
        //     'total_customers' => User::where('role', 'customer')->count(),
        //     'total_cinemas' => Cinema::count(),
        //     'today_bookings' => Booking::whereDate('created_at', today())->count(),
        //     'this_month_revenue' => Booking::whereMonth('created_at', now()->month)
        //         ->where('status', 'completed')
        //         ->sum('total_amount'),
        // ];

        return view('admin.dashboard.index');
    }
}
