<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings with search and filter functionality
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'showtime.movie'])
            ->orderBy('booking_date', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by booking status
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->paginate(15)->appends(request()->query());

        // Get booking status options for filter
        $statusOptions = [
            'Pending' => 'Chờ Xử Lý',
            'Confirmed' => 'Đã Xác Nhận',
            'Cancelled' => 'Đã Hủy',
            'Expired' => 'Hết Hạn'
        ];

        $paymentStatusOptions = [
            'Pending' => 'Chờ Thanh Toán',
            'Paid' => 'Đã Thanh Toán',
            'Failed' => 'Thanh Toán Thất Bại',
            'Refunded' => 'Đã Hoàn Tiền'
        ];

        return view('admin.bookings.index', compact(
            'bookings',
            'statusOptions',
            'paymentStatusOptions'
        ));
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        // Load all related data
        $booking->load([
            'user',
            'showtime.movie',
            'showtime.screen.cinema',
            'bookingSeats.seat',
            'bookingFoods.foodItem',
            'promotion'
        ]);

        // Get booking status options for update form
        $statusOptions = [
            'Pending' => 'Chờ Xử Lý',
            'Confirmed' => 'Đã Xác Nhận',
            'Cancelled' => 'Đã Hủy',
            'Expired' => 'Hết Hạn'
        ];

        return view('admin.bookings.show', compact('booking', 'statusOptions'));
    }

    /**
     * Update the specified booking status
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'booking_status' => 'required|in:Pending,Confirmed,Cancelled,Expired'
        ]);

        $oldStatus = $booking->booking_status;
        $booking->update([
            'booking_status' => $request->booking_status
        ]);

        // If status changed to Confirmed and payment is pending, update payment status
        if ($request->booking_status === 'Confirmed' && $booking->payment_status === 'Pending') {
            $booking->update(['payment_status' => 'Paid']);
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', "Đã cập nhật trạng thái đơn hàng từ '{$oldStatus}' thành '{$request->booking_status}'");
    }
}
