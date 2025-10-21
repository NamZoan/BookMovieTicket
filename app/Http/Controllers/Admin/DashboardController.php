<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;
use App\Models\Cinema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate total revenue from paid bookings
        $totalRevenue = Booking::where('payment_status', 'Paid')
            ->sum('final_amount');

        // Count confirmed bookings
        $totalConfirmedBookings = Booking::where('booking_status', 'Confirmed')->count();

        // Count new customers this month
        $newCustomersThisMonth = User::where('user_type', 'Customer')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get latest 10 bookings
        $latestBookings = Booking::with(['user', 'showtime.movie'])
            ->orderBy('booking_date', 'desc')
            ->limit(10)
            ->get();

        // Get revenue data for the last 30 days for chart
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Booking::where('payment_status', 'Paid')
                ->whereDate('payment_date', $date)
                ->sum('final_amount');

            $revenueData[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue
            ];
        }

        // Additional statistics
        $totalMovies = Movie::count();
        $totalCinemas = Cinema::count();
        $todayBookings = Booking::whereDate('booking_date', today())->count();
        $pendingBookings = Booking::where('booking_status', 'Pending')->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_confirmed_bookings' => $totalConfirmedBookings,
            'new_customers_this_month' => $newCustomersThisMonth,
            'total_movies' => $totalMovies,
            'total_cinemas' => $totalCinemas,
            'today_bookings' => $todayBookings,
            'pending_bookings' => $pendingBookings,
            'latest_bookings' => $latestBookings,
            'revenue_data' => $revenueData
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
