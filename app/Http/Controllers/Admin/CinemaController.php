<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    // Hiển thị danh sách rạp chiếu phim
    public function index()
    {
        $cinemas = Cinema::all();  // Lấy tất cả các rạp chiếu phim

        // Thêm thuộc tính display_image_url cho mỗi cinema (ưu tiên ảnh lưu storage nếu có)
        $cinemas->transform(function ($cinema) {
            $imagePath = $cinema->image_url ?? null; // nếu sau này có cột image_url
            $cinema->display_image_url = $imagePath
                ? asset('storage/posters/' . $imagePath)
                : asset('assets/img/default/cinema.jpg'); // placeholder mặc định
            return $cinema;
        });

        return view('admin.cinemas.index', compact('cinemas'));
    }

    // Hiển thị form tạo mới rạp chiếu phim
    public function create()
    {
        return view('admin.cinemas.create');
    }

    // Lưu rạp chiếu phim mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:50',
        ]);

        Cinema::create($request->all());

        return redirect()->route('admin.cinemas.index')->with('success', 'Cinema created successfully.');
    }

    // Hiển thị chi tiết rạp chiếu phim
    public function show($id)
    {
        $cinema = Cinema::findOrFail($id);  // Tìm kiếm rạp chiếu phim theo id
        return view('admin.cinemas.show', compact('cinema'));
    }

    // Hiển thị form chỉnh sửa rạp chiếu phim
    public function edit($id)
    {
        $cinema = Cinema::findOrFail($id);  // Tìm kiếm rạp chiếu phim theo id
        return view('admin.cinemas.edit', compact('cinema'));
    }

    // Cập nhật thông tin rạp chiếu phim
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:50',
        ]);

        $cinema = Cinema::findOrFail($id);  // Tìm kiếm rạp chiếu phim theo id
        $cinema->update($request->all());   // Cập nhật thông tin rạp chiếu phim

        return redirect()->route('admin.cinemas.index')->with('success', 'Cinema updated successfully.');
    }

    // Xóa rạp chiếu phim
    public function destroy($id)
    {
        $cinema = Cinema::findOrFail($id);  // Tìm kiếm rạp chiếu phim theo id
        $cinema->delete();  // Xóa rạp chiếu phim

        return redirect()->route('admin.cinemas.index')->with('success', 'Cinema deleted successfully.');
    }
}
