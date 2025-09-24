<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Cinema;
use App\Models\Showtime;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class MovieController extends Controller
{
    /**
     * Display a listing of movies with filtering options
     */
    public function index(Request $request)
    {
        try {
            $query = Movie::query();

            // Filter by status
            if ($request->has('status')) {
                switch ($request->status) {
                    case 'now-showing':
                        $query->where('status', 'Now Showing');
                        break;
                    case 'coming-soon':
                        $query->where('status', 'Coming Soon');
                        break;
                    case 'ended':
                        $query->where('status', 'Ended');
                        break;
                }
            }

            // Filter by genre
            if ($request->has('genre') && !empty($request->genre)) {
                $query->where('genre', 'LIKE', '%' . $request->genre . '%');
            }

            // Filter by rating
            if ($request->has('min_rating') && is_numeric($request->min_rating)) {
                $query->where('rating', '>=', $request->min_rating);
            }

            // Filter by language
            if ($request->has('language') && !empty($request->language)) {
                $query->where('language', $request->language);
            }

            // Filter by age rating
            if ($request->has('age_rating') && !empty($request->age_rating)) {
                $query->where('age_rating', $request->age_rating);
            }

            // Search by title, cast, or director
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('original_title', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('cast', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('director', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            // Sorting
            $sortBy = $request->get('sort', 'release_date');
            $sortDirection = $request->get('direction', 'desc');

            switch ($sortBy) {
                case 'title':
                    $query->orderBy('title', $sortDirection);
                    break;
                case 'rating':
                    $query->orderBy('rating', $sortDirection);
                    break;
                case 'duration':
                    $query->orderBy('duration', $sortDirection);
                    break;
                case 'release_date':
                default:
                    $query->orderBy('release_date', $sortDirection);
                    break;
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $movies = $query->paginate($perPage);

            // Get filter options for sidebar
            $genres = $this->getAvailableGenres();
            $languages = Movie::whereNotNull('language')->distinct()->pluck('language')->sort();
            $ageRatings = Movie::whereNotNull('age_rating')->distinct()->pluck('age_rating')->sort();

            // Get movie statistics
            $stats = [
                'total' => Movie::count(),
                'now_showing' => Movie::where('status', 'Now Showing')->count(),
                'coming_soon' => Movie::where('status', 'Coming Soon')->count(),
                'ended' => Movie::where('status', 'Ended')->count()
            ];

            return view('client.movies.index', compact(
                'movies',
                'genres',
                'languages',
                'ageRatings',
                'stats'
            ));
        } catch (\Exception $e) {
            \Log::error('MovieController@index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load movies. Please try again.');
        }
    }

    /**
     * Display movies currently showing
     */
    public function nowShowing(Request $request)
    {
        $query = Movie::where('status', 'Now Showing')
            ->whereHas('showtimes', function ($q) {
                $q->where('show_date', '>=', Carbon::today())
                    ->where('status', 'Active');
            });

        // Apply same filtering as index
        $this->applyFilters($query, $request);

        $movies = $query->orderBy('rating', 'desc')
            ->paginate(12);

        $genres = $this->getAvailableGenres();

        return view('client.movies.now-showing', compact('movies', 'genres'));
    }

    /**
     * Display upcoming movies
     */
    public function comingSoon(Request $request)
    {
        $query = Movie::where('status', 'Coming Soon')
            ->where('release_date', '>', Carbon::now());

        $this->applyFilters($query, $request);

        $movies = $query->orderBy('release_date', 'asc')
            ->paginate(12);

        $genres = $this->getAvailableGenres();

        return view('client.movies.coming-soon', compact('movies', 'genres'));
    }

    /**
     * Display movies by genre
     */
    public function byGenre(Request $request, $genre)
    {
        $query = Movie::where('genre', 'LIKE', '%' . $genre . '%')
            ->where('status', '!=', 'Ended');

        $this->applyFilters($query, $request);

        $movies = $query->orderBy('rating', 'desc')
            ->paginate(12);

        $genres = $this->getAvailableGenres();
        $currentGenre = $genre;

        return view('client.movies.genre', compact('movies', 'genres', 'currentGenre'));
    }

    /**
     * Search movies
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');

        if (strlen($searchTerm) < 2) {
            return redirect()->route('movies.index')
                ->with('error', 'Search term must be at least 2 characters');
        }

        $movies = Movie::where(function ($query) use ($searchTerm) {
            $query->where('title', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('original_title', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('cast', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('director', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('genre', 'LIKE', '%' . $searchTerm . '%');
        })
            ->where('status', '!=', 'Ended')
            ->orderBy('rating', 'desc')
            ->paginate(12);

        $genres = $this->getAvailableGenres();

        return view('client.movies.search', compact('movies', 'genres', 'searchTerm'));
    }

    /**
     * Display the specified movie
     */
    public function show(Request $request, Movie $movie)
    {
        try {
            // Get movie with related data
            $movie->load(['showtimes.screen.cinema', 'reviews.user']);

            // Get upcoming showtimes
            $upcomingShowtimes = $movie->showtimes()
                ->with(['screen.cinema'])
                ->where('show_date', '>=', Carbon::today())
                ->where('status', 'Active')
                ->where('available_seats', '>', 0)
                ->orderBy('show_date', 'asc')
                ->orderBy('show_time', 'asc')
                ->get()
                ->groupBy(function ($showtime) {
                    return $showtime->show_date;
                });

            // Get similar movies (same genre)
            $similarMovies = Movie::where('movie_id', '!=', $movie->movie_id)
                ->where('genre', 'LIKE', '%' . explode(',', $movie->genre)[0] . '%')
                ->where('status', 'Now Showing')
                ->orderBy('rating', 'desc')
                ->limit(6)
                ->get();

            // Get movie reviews with pagination
            $reviews = $movie->reviews()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Calculate average rating from reviews
            $averageReviewRating = $movie->reviews()->avg('rating');

            // Get available cinemas for this movie
            $availableCinemas = Cinema::whereHas('screens.showtimes', function ($q) use ($movie) {
                $q->where('movie_id', $movie->movie_id)
                    ->where('show_date', '>=', Carbon::today())
                    ->where('status', 'Active');
            })
                ->get();

            // Format cast and crew
            $cast = $movie->cast ? explode(',', $movie->cast) : [];
            $genres = $movie->genre ? explode(',', $movie->genre) : [];

            return view('client.movies.show', compact(
                'movie',
                'upcomingShowtimes',
                'similarMovies',
                'reviews',
                'averageReviewRating',
                'availableCinemas',
                'cast',
                'genres'
            ));
        } catch (\Exception $e) {
            \Log::error('MovieController@show error: ' . $e->getMessage());
            return redirect()->route('movies.index')
                ->with('error', 'Movie not found or unavailable.');
        }
    }

    /**
     * Display showtimes for a specific movie
     */
    public function showtimes(Request $request, $movie_id)
    {
        // Lấy ngày được chọn (mặc định là hôm nay)
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedCinema = $request->get('cinema');

        // Lấy thông tin phim
        $movie = Movie::findOrFail($movie_id);

        // Truy vấn lịch chiếu
        $query = $movie->showtimes()
            ->with(['screen.cinema'])
            ->where('show_date', $selectedDate)
            ->where('status', 'Active')
            ->where('available_seats', '>', 0);

        // Lọc theo rạp nếu được chọn
        if ($selectedCinema) {
            $query->whereHas('screen.cinema', function ($q) use ($selectedCinema) {
                $q->where('cinema_id', $selectedCinema);
            });
        }

        // Nhóm lịch chiếu theo rạp
        $showtimes = $query->orderBy('show_time', 'asc')
            ->get()
            ->groupBy('screen.cinema.name');

        // Lấy danh sách ngày có lịch chiếu (7 ngày tiếp theo)
        $availableDates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $availableDates[] = [
                'date' => $date->format('Y-m-d'),
                'formatted' => $date->format('d/m'),
                'day' => $this->getVietnameseDay($date->format('D')),
                'is_today' => $date->isToday()
            ];
        }

        // Lấy danh sách rạp có lịch chiếu cho phim này
        $availableCinemas = Cinema::whereHas('screens.showtimes', function ($q) use ($movie) {
            $q->where('movie_id', $movie->movie_id)
                ->where('show_date', '>=', Carbon::today())
                ->where('status', 'Active');
        })
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('client.movies.showtimes', compact(
            'movie',
            'showtimes',
            'availableDates',
            'availableCinemas',
            'selectedDate',
            'selectedCinema'
        ));
    }

    // Thêm phương thức này vào MovieController
    public function showtimesAjax(Request $request, $movie_id)
    {
        try {
            // Lấy ngày được chọn (mặc định là hôm nay)
            $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
            $selectedCinema = $request->get('cinema');

            // Lấy thông tin phim
            $movie = Movie::findOrFail($movie_id);

            // Truy vấn lịch chiếu
            $query = $movie->showtimes()
                ->with(['screen.cinema'])
                ->where('show_date', $selectedDate)
                ->where('status', 'Active')
                ->where('available_seats', '>', 0);

            // Lọc theo rạp nếu được chọn
            if ($selectedCinema) {
                $query->whereHas('screen.cinema', function ($q) use ($selectedCinema) {
                    $q->where('cinema_id', $selectedCinema);
                });
            }

            // Nhóm lịch chiếu theo rạp
            $showtimes = $query->orderBy('show_time', 'asc')
                ->get()
                ->groupBy('screen.cinema.name');

            // Render view fragment
            $html = view('client.movies.showtime-list', compact('showtimes'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $showtimes->flatten()->count(),
                'date' => $selectedDate
            ]);
        } catch (\Exception $e) {
            \Log::error('MovieController@showtimesAjax error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lịch chiếu. Vui lòng thử lại.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API endpoint for showtimes data
     */
    public function apiShowtimes(Request $request, $movie_id)
    {
        try {
            $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
            $selectedCinema = $request->get('cinema');

            $movie = Movie::findOrFail($movie_id);

            $query = $movie->showtimes()
                ->with(['screen.cinema'])
                ->where('show_date', $selectedDate)
                ->where('status', 'Active')
                ->where('available_seats', '>', 0);

            if ($selectedCinema) {
                $query->whereHas('screen.cinema', function ($q) use ($selectedCinema) {
                    $q->where('cinema_id', $selectedCinema);
                });
            }

            $showtimes = $query->orderBy('show_time', 'asc')
                ->get()
                ->groupBy('screen.cinema.name')
                ->map(function ($cinemaShowtimes, $cinemaName) {
                    return [
                        'cinema_name' => $cinemaName,
                        'cinema_info' => $cinemaShowtimes->first()->screen->cinema,
                        'showtimes' => $cinemaShowtimes->map(function ($showtime) {
                            return [
                                'showtime_id' => $showtime->showtime_id,
                                'show_time' => $showtime->show_time,
                                'show_time_formatted' => date('H:i', strtotime($showtime->show_time)),
                                'end_time' => $showtime->end_time,
                                'end_time_formatted' => date('H:i', strtotime($showtime->end_time)),
                                'available_seats' => $showtime->available_seats,
                                'base_price' => $showtime->base_price,
                                'screen_name' => $showtime->screen->screen_name,
                                'booking_url' => route('booking.seatSelection', $showtime->showtime_id)
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'movie' => [
                        'movie_id' => $movie->movie_id,
                        'title' => $movie->title,
                        'poster_url' => $movie->poster_url
                    ],
                    'showtimes' => $showtimes,
                    'selected_date' => $selectedDate,
                    'selected_cinema' => $selectedCinema,
                    'total_showtimes' => $showtimes->flatten()->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('MovieController@apiShowtimes error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải lịch chiếu.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get available dates with showtime counts
     */
    public function getAvailableDates($movie_id)
    {
        $movie = Movie::findOrFail($movie_id);
        $availableDates = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);

            // Count showtimes for this date
            $showtimeCount = $movie->showtimes()
                ->where('show_date', $date->format('Y-m-d'))
                ->where('status', 'Active')
                ->where('available_seats', '>', 0)
                ->count();

            $availableDates[] = [
                'date' => $date->format('Y-m-d'),
                'formatted' => $date->format('d/m'),
                'day' => $this->getVietnameseDay($date->format('D')),
                'is_today' => $date->isToday(),
                'is_tomorrow' => $date->isTomorrow(),
                'showtime_count' => $showtimeCount,
                'has_showtimes' => $showtimeCount > 0
            ];
        }

        return $availableDates;
    }

    /**
     * Get available cinemas for a movie
     */
    public function getAvailableCinemas($movie_id, $date = null)
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');

        return Cinema::whereHas('screens.showtimes', function ($q) use ($movie_id, $date) {
            $q->where('movie_id', $movie_id)
                ->where('show_date', $date)
                ->where('status', 'Active')
                ->where('available_seats', '>', 0);
        })
            ->withCount(['screens as showtime_count' => function ($q) use ($movie_id, $date) {
                $q->whereHas('showtimes', function ($subq) use ($movie_id, $date) {
                    $subq->where('movie_id', $movie_id)
                        ->where('show_date', $date)
                        ->where('status', 'Active')
                        ->where('available_seats', '>', 0);
                });
            }])
            ->orderBy('city')
            ->orderBy('name')
            ->get();
    }

    private function getVietnameseDay($englishDay)
    {
        $days = [
            'Mon' => 'Thứ 2',
            'Tue' => 'Thứ 3',
            'Wed' => 'Thứ 4',
            'Thu' => 'Thứ 5',
            'Fri' => 'Thứ 6',
            'Sat' => 'Thứ 7',
            'Sun' => 'Chủ nhật'
        ];

        return $days[$englishDay] ?? $englishDay;
    }

    /**
     * Display reviews for a specific movie
     */
    public function reviews(Request $request, Movie $movie)
    {
        $reviews = $movie->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $averageRating = $movie->reviews()->avg('rating');
        $totalReviews = $movie->reviews()->count();

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $movie->reviews()->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => round($percentage, 1)
            ];
        }

        return view('client.movies.reviews', compact(
            'movie',
            'reviews',
            'averageRating',
            'totalReviews',
            'ratingDistribution'
        ));
    }

    /**
     * API endpoint for movies list
     */
    public function apiIndex(Request $request)
    {
        $query = Movie::select(['movie_id', 'title', 'poster_url', 'rating', 'duration', 'genre', 'status']);

        $this->applyFilters($query, $request);

        $movies = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $movies->items(),
            'pagination' => [
                'current_page' => $movies->currentPage(),
                'last_page' => $movies->lastPage(),
                'per_page' => $movies->perPage(),
                'total' => $movies->total()
            ]
        ]);
    }

    /**
     * API endpoint for single movie
     */
    public function apiShow(Movie $movie)
    {
        $movie->load(['showtimes.screen.cinema']);

        return response()->json([
            'success' => true,
            'data' => $movie
        ]);
    }

    /**
     * Private helper methods
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->has('genre') && !empty($request->genre)) {
            $query->where('genre', 'LIKE', '%' . $request->genre . '%');
        }

        if ($request->has('min_rating') && is_numeric($request->min_rating)) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->has('language') && !empty($request->language)) {
            $query->where('language', $request->language);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('cast', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('director', 'LIKE', '%' . $searchTerm . '%');
            });
        }
    }

    private function getAvailableGenres()
    {
        return Movie::whereNotNull('genre')
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
            ->sort();
    }
}
