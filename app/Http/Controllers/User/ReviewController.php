<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the user's reviews.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $reviews = Review::with('movie')
            ->where('user_id', $user->user_id)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('user.reviews.index', compact('reviews'));
    }
}
