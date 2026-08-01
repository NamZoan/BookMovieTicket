<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    // Hiển thị danh sách tất cả các bộ phim
    public function index()
    {
        $movies = Movie::all();  // Lấy tất cả các bộ phim từ cơ sở dữ liệu

        // Chuẩn hóa URL ảnh hiển thị
        $movies->transform(function ($movie) {
            $movie->display_image_url = movie_poster_url($movie->poster_url);
            return $movie;
        });

        return view('admin.movies.index', compact('movies'));  // Trả về view với danh sách phim
    }

    // Hiển thị form tạo phim mới
    public function create()
    {
        return view('admin.movies.create');  // Trả về view tạo phim
    }

    // Xử lý dữ liệu form tạo phim mới
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:200',
            'original_title' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'duration' => 'required|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:100',
            'cast' => 'nullable|string',
            'genre' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:1|max:10',
            'poster_url' => 'nullable',
            'status' => 'required|in:Coming Soon,Now Showing,Ended',
        ]);

        // Xử lý file poster nếu có hoặc chuỗi URL
        $posterPath = null;
        $disk = config('filesystems.default', 'public');
        if ($request->hasFile('poster_url')) {
            $fileName = $request->file('poster_url')->getClientOriginalName();
            $timeStampedFileName = time() . '_' . $fileName;
            $posterPath = $request->file('poster_url')->storeAs('posters', $timeStampedFileName, $disk);
        } elseif ($request->filled('poster_url') && is_string($request->input('poster_url'))) {
            $posterPath = trim($request->input('poster_url'));
        }

        // Tạo mới bộ phim
        $movie = Movie::create([
            'title' => $request->title,
            'original_title' => $request->original_title,
            'description' => $request->description,
            'duration' => $request->duration,
            'release_date' => $request->release_date,
            'director' => $request->director,
            'cast' => $request->cast,
            'genre' => $request->genre,
            'rating' => $request->rating,
            'poster_url' => $posterPath,
            'status' => $request->status,
        ]);

        $this->clearMovieCache();

        return redirect()->route('admin.movies.edit', ['movie' => $movie->movie_id])->with('success', 'Thêm mới phim thành công.');
    }

    // Hiển thị form chỉnh sửa phim
    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        return view('admin.movies.edit', compact('movie'));
    }

    // Cập nhật thông tin phim
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        // Xác thực dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:200',
            'original_title' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'duration' => 'required|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:100',
            'cast' => 'nullable|string',
            'genre' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:1|max:10',
            'poster_url' => 'nullable',
            'status' => 'required|in:Coming Soon,Now Showing,Ended',
        ]);

        // Xử lý file poster nếu có
        $updateData = [
            'title' => $request->title,
            'original_title' => $request->original_title,
            'description' => $request->description,
            'duration' => $request->duration,
            'release_date' => $request->release_date,
            'director' => $request->director,
            'cast' => $request->cast,
            'genre' => $request->genre,
            'rating' => $request->rating,
            'status' => $request->status,
        ];

        if ($request->hasFile('poster_url')) {
            $disk = config('filesystems.default', 'public');
            // Xóa poster cũ nếu có
            if ($movie->poster_url && !Str::startsWith($movie->poster_url, ['http://', 'https://']) && Storage::disk($disk)->exists($movie->poster_url)) {
                Storage::disk($disk)->delete($movie->poster_url);
            }

            // Upload poster mới
            $fileName = time() . '_' . $request->file('poster_url')->getClientOriginalName();
            $posterPath = $request->file('poster_url')->storeAs('posters', $fileName, $disk);
            $updateData['poster_url'] = $posterPath;
        } elseif ($request->filled('poster_url') && is_string($request->input('poster_url'))) {
            $updateData['poster_url'] = trim($request->input('poster_url'));
        }

        // Cập nhật thông tin phim
        $movie->update($updateData);
        $this->clearMovieCache();

        return redirect()->route('admin.movies.edit', ['movie' => $movie->movie_id])->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);

        // Xóa poster nếu có
        if ($movie->poster_url && !Str::startsWith($movie->poster_url, ['http://', 'https://'])) {
            Storage::disk('public')->delete($movie->poster_url);
        }

        $movie->delete();
        $this->clearMovieCache();

        return redirect()->route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }

    private function clearMovieCache(): void
    {
        Cache::forget('home:featured');
        Cache::forget('home:now-showing');
        Cache::forget('home:coming-soon');
        Cache::forget('home:trending');
        Cache::forget('home:latest-trailers');
        Cache::forget('home:stats');
        Cache::forget('movies:filters');
        Cache::forget('movies:stats-tiles');
    }
}
