<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMovieViewData;
use App\Models\Movie;
use App\Models\Cinema;
use App\Models\Showtime;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    use BuildsMovieViewData;

    /**
     * Display a listing of movies with filtering options
     */
    public function index(Request $request)
    {
        try {
            $query = Movie::query()->with(['showtimes' => function ($builder) {
                $builder->select('showtime_id', 'movie_id', 'show_date', 'show_time', 'status')
                    ->where('show_date', '>=', Carbon::today());
            }]);

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

            [$sortBy, $sortDirection] = $this->resolveSort($request);

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
                case 'popularity':
                    $query->orderByDesc('updated_at')->orderBy('rating', $sortDirection);
                    break;
                case 'release_date':
                default:
                    $query->orderBy('release_date', $sortDirection);
                    break;
            }

            // Pagination
            $perPage = (int) $request->get('per_page', 12);
            if ($perPage <= 0) {
                $perPage = 12;
            }

            $movies = $query->paginate($perPage)->appends($request->query());

            // Normalize paginator items for rendering without mutating paginator internals
            $normalized = $this->normalizeMovies(collect($movies->items()));

            $filters = $this->buildFilterOptions();
            $statsTiles = $this->buildStatsTiles();
            $meta = $this->buildPaginationMeta($movies);
            $activeFilters = $this->activeFilters($request, $perPage, $sortBy, $sortDirection);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('client.movies.partials.movie-card-grid', [
                        'movies' => $normalized,
                        'emptyMessage' => __('Không tìm thấy phim phù hợp với bộ lọc hiện tại.'),
                    ])->render(),
                    'movies' => $normalized->values()->all(),
                    'meta' => $meta,
                    'activeFilters' => $activeFilters,
                    'filters' => $filters,
                ]);
            }

            // Legacy data (kept for backwards compatibility)
            $genres = $this->getAvailableGenres();
            $languages = Movie::whereNotNull('language')->distinct()->pluck('language')->sort();
            $ageRatings = Movie::whereNotNull('age_rating')->distinct()->pluck('age_rating')->sort();
            $stats = [
                'total' => Movie::count(),
                'now_showing' => Movie::where('status', 'Now Showing')->count(),
                'coming_soon' => Movie::where('status', 'Coming Soon')->count(),
                'ended' => Movie::where('status', 'Ended')->count()
            ];

            return view('client.movies.index', [
                'movies' => $movies,
                'filters' => $filters,
                'activeFilters' => $activeFilters,
                'statsTiles' => $statsTiles,
                'meta' => $meta,
                'sortOptions' => $this->sortOptions(),
                'genres' => $genres,
                'languages' => $languages,
                'ageRatings' => $ageRatings,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('MovieController@index error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Không thể tải danh sách phim. Vui lòng thử lại.'),
                ], 500);
            }

            return back()->with('error', 'Unable to load movies. Please try again.');
        }
    }

    /**
     * Display movies currently showing
     */
    public function nowShowing(Request $request)
    {
        try {
            $query = Movie::where('status', 'Now Showing')
                ->whereHas('showtimes', function ($q) {
                    $q->where('show_date', '>=', Carbon::today())
                        ->where('status', 'Active');
                });

            // Apply same filtering as index
            $this->applyFilters($query, $request);

            $movies = $query->with('showtimes')
                ->orderBy('rating', 'desc')
                ->paginate(12)
                ->appends($request->query());

            $normalized = $this->normalizeMovies(collect($movies->items()));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('client.movies.partials.movie-card-grid', [
                        'movies' => $normalized,
                        'emptyMessage' => __('Hiện chưa có phim đang chiếu phù hợp.'),
                    ])->render(),
                    'movies' => $normalized->values()->all(),
                    'meta' => $this->buildPaginationMeta($movies),
                ]);
            }

            $genres = $this->getAvailableGenres();

            return view('client.movies.now-showing', compact('movies', 'genres'));
        } catch (\Exception $e) {
            \Log::error('MovieController@nowShowing error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Không thể tải danh sách phim đang chiếu. Vui lòng thử lại.'),
                ], 500);
            }

            return back()->with('error', __('Unable to load now showing movies. Please try again.'));
        }
    }

    /**
     * Display upcoming movies
     */
    public function comingSoon(Request $request)
    {
        try {
            $query = Movie::where('status', 'Coming Soon')
                ->where('release_date', '>', Carbon::now());

            $this->applyFilters($query, $request);

            $movies = $query->with('showtimes')
                ->orderBy('release_date', 'asc')
                ->paginate(12)
                ->appends($request->query());

            $normalized = $this->normalizeMovies(collect($movies->items()));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('client.movies.partials.movie-card-grid', [
                        'movies' => $normalized,
                        'emptyMessage' => __('Hiện chưa có phim sắp chiếu phù hợp.'),
                    ])->render(),
                    'movies' => $normalized->values()->all(),
                    'meta' => $this->buildPaginationMeta($movies),
                ]);
            }

            $genres = $this->getAvailableGenres();

            return view('client.movies.coming-soon', compact('movies', 'genres'));
        } catch (\Exception $e) {
            \Log::error('MovieController@comingSoon error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Không thể tải danh sách phim sắp chiếu. Vui lòng thử lại.'),
                ], 500);
            }

            return back()->with('error', __('Unable to load coming soon movies. Please try again.'));
        }
    }

    /**
     * Display movies by genre
     */
    public function byGenre(Request $request, $genre)
    {
        try {
            $query = Movie::where('genre', 'LIKE', '%' . $genre . '%')
                ->where('status', '!=', 'Ended');

            $this->applyFilters($query, $request);

            $movies = $query->with('showtimes')
                ->orderBy('rating', 'desc')
                ->paginate(12)
                ->appends($request->query());

            $normalized = $this->normalizeMovies(collect($movies->items()));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('client.movies.partials.movie-card-grid', [
                        'movies' => $normalized,
                        'emptyMessage' => __('Không có phim thuộc thể loại này.'),
                    ])->render(),
                    'movies' => $normalized->values()->all(),
                    'meta' => $this->buildPaginationMeta($movies),
                ]);
            }

            $genres = $this->getAvailableGenres();
            $currentGenre = $genre;

            return view('client.movies.genre', compact('movies', 'genres', 'currentGenre'));
        } catch (\Exception $e) {
            \Log::error('MovieController@byGenre error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Không thể tải danh sách phim theo thể loại. Vui lòng thử lại.'),
                ], 500);
            }

            return back()->with('error', __('Unable to load genre movies. Please try again.'));
        }
    }

    /**
     * Search movies
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');

        if (strlen($searchTerm) < 2) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Từ khóa tìm kiếm phải có ít nhất 2 ký tự.'),
                ], 422);
            }
            return redirect()->route('movies.index')
                ->with('error', 'Search term must be at least 2 characters');
        }

        try {
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
                ->paginate(12)
                ->appends($request->query());

            $moviesCollection = $this->normalizeMovies(collect($movies->items()));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('client.movies.partials.movie-card-grid', [
                        'movies' => $moviesCollection,
                        'emptyMessage' => __('Không tìm thấy phim phù hợp với từ khóa.'),
                    ])->render(),
                    'movies' => $moviesCollection->values()->all(),
                    'meta' => $this->buildPaginationMeta($movies),
                ]);
            }

            $genres = $this->getAvailableGenres();

            return view('client.movies.search', compact('movies', 'genres', 'searchTerm'));
        } catch (\Exception $e) {
            \Log::error('MovieController@search error: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Không thể tìm kiếm phim vào lúc này. Vui lòng thử lại.'),
                ], 500);
            }

            return back()->with('error', __('Unable to search movies right now. Please try again.'));
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
        try {
            // Check if user can review (has a successful booking)
            $canReview = false;
            $userHasReviewed = false;

            if (Auth::check()) {
                // Check if user has a successful booking
                $hasBooking = Booking::where('user_id', Auth::id())
                    ->where('booking_status', 'Confirmed')
                    ->whereHas('showtime', function($q) use ($movie) {
                        $q->where('movie_id', $movie->movie_id);
                    })
                    ->exists();

                $canReview = $hasBooking;

                // Check if user has already reviewed
                $userHasReviewed = Review::where('user_id', Auth::id())
                    ->where('movie_id', $movie->movie_id)
                    ->exists();
            }

            // Get reviews with pagination
            $reviews = Review::with('user')
                ->where('movie_id', $movie->movie_id)
                ->latest()
                ->paginate(5);

            // Calculate statistics
            $totalReviews = Review::where('movie_id', $movie->movie_id)->count();
            $averageRating = $totalReviews > 0
                ? Review::where('movie_id', $movie->movie_id)->avg('rating')
                : 0;

            // Get rating distribution
            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $count = Review::where('movie_id', $movie->movie_id)
                    ->where('rating', $i)
                    ->count();
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $totalReviews > 0 ? ($count / $totalReviews * 100) : 0
                ];
            }

            // Render the reviews partial
            $html = view('client.movies.partials.reviews-list', compact(
                'movie',
                'reviews',
                'canReview',
                'userHasReviewed',
                'totalReviews',
                'averageRating',
                'ratingDistribution'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'stats' => [
                    'total' => $totalReviews,
                    'average' => round($averageRating, 1),
                    'distribution' => $ratingDistribution
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('MovieController@reviews error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải đánh giá.'
            ], 500);
        }
    }

    /**
     * Display reviews for a specific movie
     */

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
    private function buildFilterOptions(): array
    {
        return Cache::remember('movies:filters', now()->addMinutes(5), function () {
            return [
                'statuses' => [
                    ['label' => __('Tất cả'), 'value' => null],
                    ['label' => __('Đang chiếu'), 'value' => 'now-showing'],
                    ['label' => __('Sắp chiếu'), 'value' => 'coming-soon'],
                    ['label' => __('Đã kết thúc'), 'value' => 'ended'],
                ],
                'genres' => Movie::query()
                    ->whereNotNull('genre')
                    ->select('genre')
                    ->get()
                    ->flatMap(fn ($movie) => $this->extractGenres($movie->genre))
                    ->countBy()
                    ->sortDesc()
                    ->map(fn ($count, $genre) => [
                        'label' => $genre,
                        'value' => $genre,
                        'count' => $count,
                    ])
                    ->values()
                    ->all(),
                'languages' => Movie::query()
                    ->whereNotNull('language')
                    ->select('language', DB::raw('COUNT(*) as total'))
                    ->groupBy('language')
                    ->orderBy('language')
                    ->get()
                    ->map(fn ($row) => [
                        'label' => $row->language,
                        'value' => $row->language,
                        'count' => $row->total,
                    ])
                    ->all(),
                'age_ratings' => Movie::query()
                    ->whereNotNull('age_rating')
                    ->select('age_rating', DB::raw('COUNT(*) as total'))
                    ->groupBy('age_rating')
                    ->orderBy('age_rating')
                    ->get()
                    ->map(fn ($row) => [
                        'label' => $row->age_rating,
                        'value' => $row->age_rating,
                        'count' => $row->total,
                    ])
                    ->all(),
                'ratings' => collect([9, 8, 7, 6, 5])->map(fn ($rating) => [
                    'label' => $rating . '+',
                    'value' => (string) $rating,
                ])->all(),
            ];
        });
    }

    private function buildStatsTiles(): array
    {
        return Cache::remember('movies:stats-tiles', now()->addMinutes(5), function () {
            return [
                [
                    'label' => __('Tổng phim'),
                    'value' => Movie::count(),
                    'icon' => 'film',
                    'suffix' => '',
                ],
                [
                    'label' => __('Đang chiếu'),
                    'value' => Movie::where('status', 'Now Showing')->count(),
                    'icon' => 'play-circle',
                    'suffix' => '',
                ],
                [
                    'label' => __('Sắp chiếu'),
                    'value' => Movie::where('status', 'Coming Soon')->count(),
                    'icon' => 'clock',
                    'suffix' => '',
                ],
                [
                    'label' => __('Đánh giá cao'),
                    'value' => Movie::where('rating', '>=', 8)->count(),
                    'icon' => 'star',
                    'suffix' => '+',
                ],
            ];
        });
    }

    private function buildPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }

    private function activeFilters(Request $request, int $perPage, string $sortBy, string $sortDirection): array
    {
        return [
            'status' => $request->get('status'),
            'genre' => $request->get('genre'),
            'min_rating' => $request->get('min_rating'),
            'language' => $request->get('language'),
            'age_rating' => $request->get('age_rating'),
            'search' => $request->get('search'),
            'per_page' => $perPage,
            'sort' => $sortBy,
            'direction' => $sortDirection,
        ];
    }

    private function sortOptions(): array
    {
        return [
            'release_date-desc' => __('Mới nhất'),
            'release_date-asc' => __('Cũ nhất'),
            'rating-desc' => __('Đánh giá cao'),
            'rating-asc' => __('Đánh giá thấp'),
            'title-asc' => __('A → Z'),
            'title-desc' => __('Z → A'),
            'duration-desc' => __('Thời lượng dài'),
            'duration-asc' => __('Thời lượng ngắn'),
            'popularity-desc' => __('Phổ biến'),
        ];
    }

    private function resolveSort(Request $request): array
    {
        $sort = $request->get('sort', 'release_date');
        $direction = $request->get('direction', 'desc');

        if (str_contains($sort, '-')) {
            [$sort, $forcedDirection] = explode('-', $sort, 2);
            $direction = $forcedDirection;
        }

        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return [$sort, $direction];
    }

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
