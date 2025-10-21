<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Booking::with(['showtime.movie', 'showtime.screen.cinema'])
            ->where('user_id', $user->user_id)
            ->orderBy('booking_date', 'desc');

        // Filter by booking status
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->paginate(10)->appends(request()->query());

        // Get filter options
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

        return view('user.bookings.index', compact(
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
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        // Load all related data
        $booking->load([
            'showtime.movie',
            'showtime.screen.cinema',
            'bookingSeats.seat',
            'bookingFoods.foodItem',
            'promotion'
        ]);

        return view('user.bookings.show', compact('booking'));
    }
}
