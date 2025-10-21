<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
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
                foreach ((array)$amenities as $amenity) {
                    $query->whereJsonContains('amenities', $amenity);
                }
            }
        }

        // Order and eager load screens (and its showtimes for quick preview)
        $query->with(['screens' => function ($q) {
            $q->with(['showtimes' => function ($s) {
                $s->where('show_date', '>=', now()->toDateString())->orderBy('show_date')->limit(5);
            }])->limit(5);
        }])->orderBy('name');

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

        return view('client.cinemas.index', compact('cinemas', 'cities'));
    }

    // Show cinema detail page
    public function show(Cinema $cinema)
    {
        // Eager load screens and upcoming showtimes
        $cinema->load(['screens' => function ($q) {
            $q->with(['showtimes' => function ($s) {
                $s->where('show_date', '>=', now()->toDateString())->orderBy('show_date');
            }]);
        }]);

        // Provide a simple resource array to the view
        $data = [
            'cinema' => $cinema,
            'screens' => $cinema->screens,
        ];

        return view('client.cinemas.show', $data);
    }

    // Return showtimes grouped by date/movie as JSON (ajax)
    public function showtimes(Request $request, Cinema $cinema)
    {
        $date = $request->input('date');
        $movieId = $request->input('movie_id');

        $showtimesQuery = $cinema->screens()->with(['showtimes' => function ($q) use ($date, $movieId) {
            $q->where('show_date', '>=', $date ?? now()->toDateString());
            if ($movieId) {
                $q->where('movie_id', $movieId);
            }
            $q->orderBy('show_date')->orderBy('show_time');
        }]);

        $screens = $showtimesQuery->get();

        // Group showtimes by date then by movie
        $grouped = [];
        foreach ($screens as $screen) {
            foreach ($screen->showtimes as $s) {
                $d = $s->show_date;
                $grouped[$d][$s->movie_id]['movie'] = $s->movie ?? null;
                $grouped[$d][$s->movie_id]['showtimes'][] = $s;
            }
        }

        // If AJAX (client expects HTML fragment), render partial
        if ($request->ajax()) {
            $html = view('client.cinemas.partials._showtime_accordion', ['grouped' => $grouped])->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        return response()->json(['data' => $grouped]);
    }

    // Filter cinemas by city (reuse index)
    public function byCity(Request $request, $city)
    {
        $request->merge(['city' => $city]);
        return $this->index($request);
    }
}
