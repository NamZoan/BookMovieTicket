<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CinemaController extends Controller
{
    // List cinemas with filters and optional AJAX response
    public function index(Request $request)
    {
        $perPage = 12;
        $query = Cinema::query();

        // Search by name/address
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // City filter
        if ($city = $request->input('city')) {
            $query->where('city', $city);
        }

        // Amenity filters (if column exists as JSON), otherwise ignored gracefully
        if ($amenities = $request->input('amenities')) {
            // attempt to filter if amenities column exists
            if (Schema::hasColumn('cinemas', 'amenities')) {
                foreach ((array) $amenities as $amenity) {
                    $query->whereJsonContains('amenities', $amenity);
                }
            }
        }

        // Order and eager load screens (and its showtimes for quick preview)
        $query->with([
            'screens' => function ($q) {
                $q->with([
                    'showtimes' => function ($s) {
                        $s->where('show_date', '>=', Carbon::today()->toDateString())
                            ->where('available_seats', '>', 0)
                            ->where('status', 'Active')
                            ->orderBy('show_date')
                            ->orderBy('show_time')
                            ->with('movie')
                            ->limit(10);
                    }
                ])->select('screen_id', 'cinema_id', 'screen_name')->limit(5);
            }
        ])->orderBy('name');

        $cacheKey = 'cinemas_index_' . md5(serialize($request->all()) . "_page_{$request->get('page', 1)}");
        $cinemas = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($query, $perPage) {
            return $query->paginate($perPage);
        });

        // If AJAX (filtering or load more), return partial HTML
        if ($request->ajax()) {
            $html = view('client.cinemas.partials._cinema_cards', compact('cinemas'))->render();
            return response()->json([
                'html' => $html,
                'pagination' => (string) $cinemas->links('vendor.pagination.bootstrap-5')
            ]);
        }

        // For non-AJAX return full view
        // Gather list of distinct cities for filters
        $cities = Cinema::select('city')->distinct()->pluck('city')->filter()->values();

        $stats = [
            'cinemas' => Cinema::count(),
            'cities' => Cinema::distinct('city')->count('city'),
            'active_showtimes' => Showtime::where('show_date', '>=', Carbon::today()->toDateString())
                ->where('status', 'Active')
                ->count(),
            'active_movies' => Showtime::where('show_date', '>=', Carbon::today()->toDateString())
                ->where('status', 'Active')
                ->distinct('movie_id')
                ->count('movie_id'),
        ];

        return view('client.cinemas.index', compact('cinemas', 'cities', 'stats'));
    }

    // Show cinema detail page
    public function show(Cinema $cinema)
    {
        // Eager load screens and upcoming showtimes
        $cinema->load([
            'screens' => function ($q) {
                $q->with([
                    'showtimes' => function ($s) {
                        $s->where('show_date', '>=', Carbon::today()->toDateString())
                            ->where('status', 'Active')
                            ->orderBy('show_date')
                            ->orderBy('show_time')
                            ->with('movie');
                    }
                ])->select('screen_id', 'cinema_id', 'screen_name');
            }
        ]);

        $groupedShowtimes = $this->buildShowtimeSchedule($cinema->screens);

        return view('client.cinemas.show', [
            'cinema' => $cinema,
            'screens' => $cinema->screens,
            'groupedShowtimes' => $groupedShowtimes,
        ]);
    }

    // Return showtimes grouped by date/movie as JSON (ajax) or HTML view
    public function showtimes(Request $request, Cinema $cinema)
    {
        $date = $request->input('date');
        $movieId = $request->input('movie_id');

        $showtimesQuery = $cinema->screens()->with([
            'showtimes' => function ($q) use ($date, $movieId) {
                $q->where('show_date', '>=', $date ?? Carbon::today()->toDateString())
                    ->where('status', 'Active')
                    ->where('available_seats', '>', 0);
                if ($movieId) {
                    $q->where('movie_id', $movieId);
                }
                $q->orderBy('show_date')
                    ->orderBy('show_time')
                    ->with('movie');
            }
        ]);


        $screens = $showtimesQuery->get();

        // Group showtimes by date then by movie
        $grouped = $this->buildShowtimeSchedule($screens);
        // If AJAX (client expects HTML fragment), render partial
        if ($request->ajax()) {
            $html = view('client.cinemas.partials._showtime_accordion', ['grouped' => $grouped])->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        // For regular requests, return a full HTML view
        return view('client.cinemas.showtimes', [
            'cinema' => $cinema,
            'groupedShowtimes' => $grouped,
        ]);
    }

    // Filter cinemas by city (reuse index)
    public function byCity(Request $request, $city)
    {
        $request->merge(['city' => $city]);
        return $this->index($request);
    }

    /**
     * Build a structured map of showtimes grouped by date and movie.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection  $screens
     * @return array
     */
    protected function buildShowtimeSchedule($screens): array
    {
        $schedule = [];

        // Defensive: if there are no screens, return an empty schedule early
        if (empty($screens) || collect($screens)->isEmpty()) {
            return $schedule;
        }
        foreach ($screens as $screen) {
            foreach ($screen->showtimes as $showtime) {

                if ($showtime->status !== 'Active') {
                    continue;
                }

                $date = $showtime->show_date instanceof Carbon
                    ? $showtime->show_date->format('Y-m-d')
                    : Carbon::parse($showtime->show_date)->format('Y-m-d');

                $time = $showtime->show_time instanceof Carbon
                    ? $showtime->show_time->format('H:i')
                    : Carbon::parse($showtime->show_time)->format('H:i');

                $schedule[$date][$showtime->movie_id]['movie'] = $showtime->movie;
                $schedule[$date][$showtime->movie_id]['showtimes'][] = [
                    'id' => $showtime->showtime_id,
                    'time' => $time,
                    'screen' => $screen->screen_name,
                    'available_seats' => $showtime->available_seats,
                    'price' => $showtime->price_seat_normal,
                ];
            }
        }

        ksort($schedule);

        return $schedule;
    }
}
