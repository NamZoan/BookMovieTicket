<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMovieViewData;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    use BuildsMovieViewData;

    public function index(Request $request)
    {
        try {
            $featuredMovies = Cache::remember('home:featured', now()->addMinutes(5), function () {
                return $this->normalizeMovies(
                    Movie::featured()
                        ->with('showtimes')
                        ->limit(5)
                        ->get()
                )->all();
            });

            $nowShowingMovies = Cache::remember('home:now-showing', now()->addMinutes(3), function () {
                return $this->normalizeMovies(
                    Movie::nowShowing()
                        ->orderByDesc('release_date')
                        ->limit(12)
                        ->get()
                )->all();
            });

            $comingSoonMovies = Cache::remember('home:coming-soon', now()->addMinutes(5), function () {
                return $this->normalizeMovies(
                    Movie::comingSoon()
                        ->limit(12)
                        ->get()
                )->all();
            });

            $trendingMovies = Cache::remember('home:trending', now()->addMinutes(5), function () {
                return $this->normalizeMovies(
                    Movie::trending()
                        ->limit(8)
                        ->get()
                )->all();
            });

            $latestTrailers = Cache::remember('home:latest-trailers', now()->addMinutes(10), function () {
                return Movie::nowShowing()
                    ->whereNotNull('trailer_url')
                    ->orderByDesc('release_date')
                    ->limit(4)
                    ->get()
                    ->map(fn (Movie $movie) => [
                        'movie' => $this->mapMovieForCard($movie),
                        'trailer_url' => $this->resolveTrailerUrl($movie->trailer_url),
                    ])
                    ->all();
            });

            $movieStats = Cache::remember('home:stats', now()->addMinutes(15), function () {
                return $this->buildHomepageStats();
            });

            $genreFilters = Cache::remember('home:genres', now()->addMinutes(30), function () {
                return $this->buildGenreList();
            });
        } catch (Throwable $exception) {
            Log::error('HomeController@index cache bootstrap failed', ['exception' => $exception]);
            $featuredMovies = $nowShowingMovies = $comingSoonMovies = $trendingMovies = $latestTrailers = [];
            $movieStats = $this->buildEmptyStats();
            $genreFilters = [];
        }

        $todayShowtimes = Showtime::with(['movie', 'screen.cinema'])
            ->whereDate('show_date', Carbon::today())
            ->where('status', 'Active')
            ->where('available_seats', '>', 0)
            ->orderBy('show_time')
            ->limit(12)
            ->get()
            ->map(fn ($showtime) => $this->mapShowtime($showtime))
            ->all();

        $heroSlides = collect($featuredMovies)
            ->map(function (array $movie) {
                return array_merge($movie, [
                    'background_image' => $movie['poster_url'],
                    'cta' => [
                        'label' => __('Đặt vé ngay'),
                        'url' => $movie['details_url'],
                    ],
                ]);
            })
            ->all();

        $homepageViewData = [
            'heroSlides' => $heroSlides,
            'nowShowingMovies' => $nowShowingMovies,
            'comingSoonMovies' => $comingSoonMovies,
            'trendingMovies' => $trendingMovies,
            'movieStats' => $movieStats,
            'quickShowtimes' => $todayShowtimes,
            'genreFilters' => $genreFilters,
            'latestTrailers' => $latestTrailers,
            'testimonials' => $this->buildTestimonials(),
        ];

        if (Auth::check()) {
            $homepageViewData['recommendations'] = $this->buildRecommendationsForUser((int) Auth::id());
        }

        return view('client.home', $homepageViewData);
    }

    public function getMoviesByGenre(Request $request)
    {
        $genre = $request->get('genre');

        $movies = Movie::nowShowing()
            ->genre($genre)
            ->topRated()
            ->limit(12)
            ->get();

        $movieCards = $this->normalizeMovies($movies);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'genre' => $genre,
                'movies' => $movieCards->values()->all(),
                'html' => view('client.movies.partials.movie-card-grid', [
                    'movies' => $movieCards,
                ])->render(),
            ]);
        }

        return redirect()->route('movies.index', ['genre' => $genre]);
    }

    public function searchMovies(Request $request)
    {
        $query = (string) $request->get('q', '');

        if (mb_strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => __('Vui lòng nhập ít nhất 2 ký tự để tìm kiếm.'),
            ], 422);
        }

        $movies = Movie::query()
            ->where(function ($subQuery) use ($query) {
                $subQuery->where('title', 'LIKE', '%' . $query . '%')
                    ->orWhere('original_title', 'LIKE', '%' . $query . '%')
                    ->orWhere('cast', 'LIKE', '%' . $query . '%')
                    ->orWhere('director', 'LIKE', '%' . $query . '%');
            })
            ->where('status', '!=', 'Ended')
            ->orderByDesc('rating')
            ->limit(8)
            ->get()
            ->map(fn (Movie $movie) => [
                'id' => $movie->movie_id,
                'title' => $movie->title,
                'poster_url' => $this->resolvePosterUrl($movie->poster_url),
                'rating' => $this->formatRating($movie->rating),
                'status' => $movie->status,
                'details_url' => route('movies.show', $movie->movie_id),
            ])
            ->all();

        return response()->json([
            'success' => true,
            'movies' => $movies,
        ]);
    }

    public function getMovieShowtimes(Request $request, $movieId)
    {
        $dateInput = $request->get('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::today();

        $showtimes = Showtime::with(['screen.cinema'])
            ->where('movie_id', $movieId)
            ->whereDate('show_date', $date)
            ->where('status', 'Active')
            ->where('available_seats', '>', 0)
            ->orderBy('show_time')
            ->get()
            ->groupBy(fn ($showtime) => optional(optional($showtime->screen)->cinema)->name ?? __('Rạp khác'));

        $payload = $showtimes->map(function (Collection $cinemaShowtimes, $cinemaName) {
            $cinema = optional(optional($cinemaShowtimes->first())->screen)->cinema;

            return [
                'cinema' => $cinemaName,
                'address' => optional($cinema)->address ?: optional($cinema)->city,
                'showtimes' => $cinemaShowtimes->map(function ($showtime) {
                    return [
                        'id' => $showtime->showtime_id,
                        'time' => optional($showtime->show_time)->format('H:i'),
                        'available_seats' => $showtime->available_seats,
                        'screen' => optional($showtime->screen)->screen_name,
                        'price' => $showtime->price_seat_normal ?? $showtime->base_price,
                        'booking_url' => route('booking.seatSelection', ['showtime' => $showtime->showtime_id]),
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'date' => $date->format('Y-m-d'),
            'showtimes' => $payload,
        ]);
    }

    public function getCinemaLocations()
    {
        $cinemas = Cinema::select('cinema_id', 'name', 'address', 'city')
            ->get()
            ->groupBy('city')
            ->map(fn ($group, $city) => [
                'city' => $city,
                'cinemas' => $group->map(fn ($cinema) => [
                    'id' => $cinema->cinema_id,
                    'name' => $cinema->name,
                    'address' => $cinema->address,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'cinemas' => $cinemas,
        ]);
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:191',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // If a 'contacts' table exists, store the message
            if (Schema::hasTable('contacts')) {
                DB::table('contacts')->insert([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Send email to admin (fallback)
                $admin = config('mail.from.address') ?: (env('MAIL_FROM_ADDRESS') ?: 'support@myshowz.example');
                Mail::to($admin)->send(new ContactMessage($data));
            }

            return redirect()->back()->with('contact_success', 'Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn và sẽ liên hệ sớm.');
        } catch (Throwable $e) {
            Log::error('Contact form submit failed', ['exception' => $e]);
            return redirect()->back()->withInput()->with('contact_error', 'Không thể gửi liên hệ vào lúc này. Vui lòng thử lại sau.');
        }
    }

    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email',
        ]);

        try {
            DB::table('newsletter_subscriptions')->insert([
                'email' => $request->email,
                'subscribed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Đăng ký nhận bản tin thành công!'),
            ]);
        } catch (Throwable $exception) {
            Log::error('Newsletter subscription failed', ['exception' => $exception]);

            return response()->json([
                'success' => false,
                'message' => __('Không thể đăng ký. Vui lòng thử lại.'),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */
    protected function buildHomepageStats(): array
    {
        return [
            [
                'label' => __('Phim đang chiếu'),
                'value' => Movie::nowShowing()->count(),
                'suffix' => '+',
            ],
            [
                'label' => __('Sắp ra mắt'),
                'value' => Movie::comingSoon()->count(),
                'suffix' => '',
            ],
            [
                'label' => __('Tổng số rạp'),
                'value' => Cinema::count(),
                'suffix' => '',
            ],
            [
                'label' => __('Suất chiếu hôm nay'),
                'value' => Showtime::whereDate('show_date', Carbon::today())->count(),
                'suffix' => '',
            ],
        ];
    }

    protected function buildEmptyStats(): array
    {
        return [
            ['label' => __('Phim đang chiếu'), 'value' => 0, 'suffix' => ''],
            ['label' => __('Sắp ra mắt'), 'value' => 0, 'suffix' => ''],
            ['label' => __('Tổng số rạp'), 'value' => 0, 'suffix' => ''],
            ['label' => __('Suất chiếu hôm nay'), 'value' => 0, 'suffix' => ''],
        ];
    }

    protected function buildGenreList(): array
    {
        return Movie::query()
            ->whereNotNull('genre')
            ->pluck('genre')
            ->flatMap(fn ($genres) => $this->extractGenres($genres))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected function buildTestimonials(): array
    {
        return [
            [
                'name' => 'Lan Anh',
                'role' => __('Khách hàng thân thiết'),
                'quote' => __('Giao diện mới giúp tôi tìm suất chiếu nhanh hơn rất nhiều. Đặt vé chỉ trong vài bước!'),
                'avatar' => asset('assets/img/default/cinema.jpg'),
            ],
            [
                'name' => 'Minh Khoa',
                'role' => __('Thành viên hạng Gold'),
                'quote' => __('Các đề xuất phim theo sở thích thật chính xác. Tôi luôn tìm thấy phim hay để xem.'),
                'avatar' => asset('assets/img/default/cinema.jpg'),
            ],
            [
                'name' => 'Quỳnh Nhi',
                'role' => __('Người yêu điện ảnh'),
                'quote' => __('Trải nghiệm mượt mà, thông tin rõ ràng. Tôi rất thích phần trailer mới trên trang chủ.'),
                'avatar' => asset('assets/img/default/cinema.jpg'),
            ],
        ];
    }

    protected function buildRecommendationsForUser(int $userId): array
    {
        $preferredGenres = Booking::query()
            ->where('user_id', $userId)
            ->with(['showtime.movie'])
            ->latest('booking_date')
            ->take(20)
            ->get()
            ->pluck('showtime.movie')
            ->filter()
            ->flatMap(fn ($movie) => $this->extractGenres(optional($movie)->genre))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3)
            ->values()
            ->all();

        if (empty($preferredGenres)) {
            return [];
        }

        $recommendations = Movie::query()
            ->whereIn('status', ['Now Showing', 'Coming Soon'])
            ->where(function ($query) use ($preferredGenres) {
                foreach ($preferredGenres as $genre) {
                    $query->orWhere('genre', 'LIKE', '%' . $genre . '%');
                }
            })
            ->topRated()
            ->limit(6)
            ->get();

        return $this->normalizeMovies($recommendations)->all();
    }
}
