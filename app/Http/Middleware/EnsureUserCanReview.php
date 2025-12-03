<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use App\Models\Movie;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserCanReview
{
    /**
     * Ensure the authenticated user has a confirmed booking for the movie.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $movieParam = $request->route('movie');

        // Resolve movie id from route model binding or raw id.
        $movieId = $movieParam instanceof Movie ? $movieParam->movie_id : $movieParam;

        $hasBooking = Booking::where('user_id', $user->user_id)
            ->where('booking_status', 'Confirmed')
            ->whereHas('showtime', function ($q) use ($movieId) {
                $q->where('movie_id', $movieId);
            })
            ->exists();

        if (!$hasBooking) {
            $message = 'Bạn cần đặt vé xem phim này trước khi đánh giá.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403);
            }

            return redirect()->back()->with('error', $message);
        }

        return $next($request);
    }
}
