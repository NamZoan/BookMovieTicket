<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'movie'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $this->authorize('approve', $review);

        $review->update(['is_approved' => true]);

        // Notify user that their review was approved
        // $review->user->notify(new ReviewApprovedNotification($review));

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được duyệt.'
            ]);
        }

        return back()->with('success', 'Đánh giá đã được duyệt.');
    }

    public function reject(Review $review)
    {
        $this->authorize('approve', $review);

        $review->update(['is_approved' => false]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối đánh giá.'
            ]);
        }

        return back()->with('success', 'Đã từ chối đánh giá.');
    }

    public function show(Review $review)
    {
        $review->load(['user', 'movie']);
        return view('admin.reviews.show', compact('review'));
    }
}