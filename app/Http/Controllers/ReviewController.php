<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Movie $movie)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        try {
            // Check if user has booked this movie
            $hasBooking = Booking::where('user_id', Auth::id())
                ->where('booking_status', 'Confirmed')
                ->whereHas('showtime', function($q) use ($movie) {
                    $q->where('movie_id', $movie->movie_id);
                })
                ->exists();

            if (!$hasBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đặt vé xem phim này để có thể đánh giá.'
                ], 403);
            }

            // Check if user has already reviewed
            $existingReview = Review::where('user_id', Auth::id())
                ->where('movie_id', $movie->movie_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đánh giá phim này rồi.'
                ], 403);
            }

            $review = new Review();
            $review->user_id = Auth::id();
            $review->movie_id = $movie->movie_id;
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được ghi nhận!'
            ]);
        } catch (\Exception $e) {
            Log::error('Review creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Movie $movie, Review $review)
    {
        // Allow deletion only by the review owner or an admin
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không được phép'], 403);
        }

        if ($review->user_id !== $user->user_id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xoá đánh giá này.'], 403);
        }

        try {
            $review->delete();
            return response()->json(['success' => true, 'message' => 'Đã xoá đánh giá thành công.']);
        } catch (\Exception $e) {
            Log::error('Review deletion failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xoá đánh giá.'], 500);
        }
    }
}
