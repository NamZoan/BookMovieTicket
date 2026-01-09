<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    // Hiển thị danh sách tất cả các bộ phim
    public function index()
    {
        $movies = Movie::all();  // Lấy tất cả các bộ phim từ cơ sở dữ liệu

        // Chuẩn hóa URL ảnh hiển thị
        $movies->transform(function ($movie) {
            $posterPath = $movie->poster_url; // có thể là null hoặc 'posters/xxx.jpg'
            $movie->display_image_url = $posterPath
                ? asset('storage/' . ltrim($posterPath, '/'))
                : asset('assets/img/default/cinema.jpg');
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
            'poster_url' => 'nullable|image|max:2048', // Kiểm tra file ảnh
            'status' => 'required|in:Coming Soon,Now Showing,Ended',
        ]);

        // Xử lý file poster nếu có
        $posterPath = null;
        if ($request->hasFile('poster_url')) {
            $fileName = $request->file('poster_url')->getClientOriginalName();
            $timeStampedFileName = time() . '_' . $fileName;
            $posterPath = $request->file('poster_url')->storeAs('posters', $timeStampedFileName, 'public');
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
            'poster_url' => 'nullable|image|max:2048',
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
            // Xóa poster cũ nếu có
            if ($movie->poster_url && Storage::disk('public')->exists($movie->poster_url)) {
                Storage::disk('public')->delete($movie->poster_url);
            }

            // Upload poster mới
            $fileName = time() . '_' . $request->file('poster_url')->getClientOriginalName();
            $posterPath = $request->file('poster_url')->storeAs('posters', $fileName, 'public');
            $updateData['poster_url'] = $posterPath;
        }

        // Cập nhật thông tin phim
        $movie->update($updateData);

        return redirect()->route('admin.movies.edit', ['movie' => $movie->movie_id])->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);

        // Xóa poster nếu có
        if ($movie->poster_url) {
            Storage::disk('public')->delete($movie->poster_url);
        }

        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }
}
