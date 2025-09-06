<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Cinema;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the home page with featured movies and data
     */
    public function index()
    {
        try {
            // Get movies that are currently showing
            $nowShowingMovies = Movie::where('status', 'Now Showing')
                ->orderBy('rating', 'desc')
                ->limit(8)
                ->get();

            // Get upcoming movies (coming soon)
            $upcomingMovies = Movie::where('status', 'Coming Soon')
                ->where('release_date', '>', Carbon::now())
                ->orderBy('release_date', 'asc')
                ->limit(6)
                ->get();

            // Get popular movies based on rating
            $popularMovies = Movie::where('status', 'Now Showing')
                ->where('rating', '>=', 7.0)
                ->orderBy('rating', 'desc')
                ->limit(4)
                ->get();

            // Get featured movies for slider (high rating movies)
            $featuredMovies = Movie::whereIn('status', ['Now Showing', 'Coming Soon'])
                ->where('rating', '>=', 8.0)
                ->orderBy('rating', 'desc')
                ->limit(4)
                ->get();

            // Get movie statistics for homepage
            $movieStats = [
                'total_movies' => Movie::count(),
                'now_showing' => Movie::where('status', 'Now Showing')->count(),
                'coming_soon' => Movie::where('status', 'Coming Soon')->count(),
                'total_cinemas' => Cinema::count()
            ];

            // Get today's showtimes for quick booking
            $todayShowtimes = Showtime::with(['movie', 'screen.cinema'])
                ->where('show_date', Carbon::today())
                ->where('status', 'Active')
                ->where('available_seats', '>', 0)
                ->orderBy('show_time', 'asc')
                ->limit(10)
                ->get();

            // Get movies by genre for filtering
            $genres = Movie::whereNotNull('genre')
                ->select('genre')
                ->distinct()
                ->pluck('genre')
                ->map(function ($genre) {
                    return explode(',', $genre);
                })
                ->flatten()
                ->map('trim')
                ->unique()
                ->values()
                ->take(8);

            // Get latest movie trailers
            $latestTrailers = Movie::whereNotNull('trailer_url')
                ->where('status', 'Now Showing')
                ->orderBy('release_date', 'desc')
                ->limit(4)
                ->get();

            return view('client.home', compact(
                'nowShowingMovies',
                'upcomingMovies',
                'popularMovies',
                'featuredMovies',
                'movieStats',
                'todayShowtimes',
                'genres',
                'latestTrailers'
            ));

        } catch (\Exception $e) {
            // Log error and return with empty data
            \Log::error('HomeController@index error: ' . $e->getMessage());

            return view('client.home', [
                'nowShowingMovies' => collect([]),
                'upcomingMovies' => collect([]),
                'popularMovies' => collect([]),
                'featuredMovies' => collect([]),
                'movieStats' => [
                    'total_movies' => 0,
                    'now_showing' => 0,
                    'coming_soon' => 0,
                    'total_cinemas' => 0
                ],
                'todayShowtimes' => collect([]),
                'genres' => collect([]),
                'latestTrailers' => collect([])
            ]);
        }
    }

    /**
     * Get movies by genre for AJAX requests
     */
    public function getMoviesByGenre(Request $request)
    {
        $genre = $request->get('genre');

        $movies = Movie::where('status', 'Now Showing')
            ->where('genre', 'LIKE', '%' . $genre . '%')
            ->orderBy('rating', 'desc')
            ->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'movies' => $movies->items(),
                'pagination' => [
                    'current_page' => $movies->currentPage(),
                    'last_page' => $movies->lastPage(),
                    'has_more' => $movies->hasMorePages()
                ]
            ]);
        }

        return redirect()->route('movies.index', ['genre' => $genre]);
    }

    /**
     * Search movies for quick search functionality
     */
    public function searchMovies(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least 2 characters'
            ]);
        }

        $movies = Movie::where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('original_title', 'LIKE', '%' . $query . '%')
                  ->orWhere('cast', 'LIKE', '%' . $query . '%')
                  ->orWhere('director', 'LIKE', '%' . $query . '%');
            })
            ->where('status', '!=', 'Ended')
            ->select('movie_id', 'title', 'poster_url', 'rating', 'status', 'release_date')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'movies' => $movies
        ]);
    }

    /**
     * Get movie showtimes for quick booking
     */
    public function getMovieShowtimes(Request $request, $movieId)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $showtimes = Showtime::with(['screen.cinema'])
            ->where('movie_id', $movieId)
            ->where('show_date', $date)
            ->where('status', 'Active')
            ->where('available_seats', '>', 0)
            ->orderBy('show_time', 'asc')
            ->get()
            ->groupBy('screen.cinema.name');

        return response()->json([
            'success' => true,
            'showtimes' => $showtimes
        ]);
    }

    /**
     * Get cinema locations for homepage map/list
     */
    public function getCinemaLocations()
    {
        $cinemas = Cinema::select('cinema_id', 'name', 'address', 'city')
            ->get()
            ->groupBy('city');

        return response()->json([
            'success' => true,
            'cinemas' => $cinemas
        ]);
    }

    /**
     * Newsletter subscription
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email'
        ]);

        try {
            // Assuming you have a newsletter_subscriptions table
            DB::table('newsletter_subscriptions')->insert([
                'email' => $request->email,
                'subscribed_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed. Please try again.'
            ], 500);
        }
    }
}
