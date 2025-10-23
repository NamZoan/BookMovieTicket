<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Movie $movie)
    {
        $user = Auth::user();
        // Current user's review (if any)
        $userReview = null;
        $userId = $user ? $user->user_id : null;

        if ($userId) {
            $userReview = Review::with('user')
                ->where('movie_id', $movie->movie_id)
                ->where('user_id', $userId)
                ->first();
        }

        // Approved reviews from other users (paginated)
        $othersQuery = Review::with('user')
            ->where('movie_id', $movie->movie_id)
            ->where('is_approved', true);

        if ($userId) {
            $othersQuery->where('user_id', '!=', $userId);
        }

        $othersReviews = $othersQuery->latest()->paginate(10);

        $averageRating = Review::where('movie_id', $movie->movie_id)
            ->where('is_approved', true)
            ->avg('rating');

        // Rating counts for approved reviews (1-10 scale)
        $ratingCounts = Review::where('movie_id', $movie->movie_id)
            ->where('is_approved', true)
            ->select('rating', DB::raw('count(*) as cnt'))
            ->groupBy('rating')
            ->pluck('cnt', 'rating')
            ->toArray();

        $approvedTotal = array_sum($ratingCounts);

        // If AJAX or requested as HTML fragment, return the partial view only
        if (request()->ajax() || request()->wantsJson() || str_contains(request()->header('Accept', ''), 'text/html')) {
            return view('client.movies.partials.reviews', compact('movie', 'othersReviews', 'userReview', 'averageRating', 'ratingCounts', 'approvedTotal'));
        }

        return view('client.movies.reviews', compact('movie', 'othersReviews', 'userReview', 'averageRating', 'ratingCounts', 'approvedTotal'));
    }

    public function store(StoreReviewRequest $request, Movie $movie)
    {
        $this->authorize('create', [Review::class, $movie]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movie->movie_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false
        ]);

        // Notify admin about new review
        // Notification::send(User::admin()->get(), new NewReviewNotification($review));

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt.',
                'review' => $review->load('user')
            ]);
        }

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    }

    public function update(UpdateReviewRequest $request, Movie $movie, Review $review)
    {
        $this->authorize('update', $review);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false // Reset approval status on update
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được cập nhật và đang chờ duyệt lại.',
                'review' => $review->fresh()->load('user')
            ]);
        }

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Đánh giá đã được cập nhật và đang chờ duyệt lại.');
    }

    public function destroy(Movie $movie, Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được xóa.'
            ]);
        }

        return redirect()->route('movies.show', $movie)
            ->with('success', 'Đánh giá đã được xóa.');
    }
}