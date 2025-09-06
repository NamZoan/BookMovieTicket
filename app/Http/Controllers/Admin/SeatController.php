<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Screen;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    // Hiển thị danh sách ghế
    public function index()
    {
        $seats = Seat::with('screen')->get();  // Lấy tất cả ghế và thông tin phòng chiếu
        return view('admin.seats.index', compact('seats'));
    }

    // Hiển thị form tạo ghế mới
    public function create()
    {
        $screens = Screen::all();  // Lấy tất cả các phòng chiếu
        return view('admin.seats.create', compact('screens'));
    }

    // Lưu ghế mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        // Validate các trường đầu vào
        $request->validate([
            'screen_id'  => 'required|exists:screens,screen_id',
            'row_name'   => 'required|string|max:5',
            'seat_number' => 'required|integer|min:1',
            'seat_type'  => 'required|in:Regular,VIP,Couple',
        ]);

        // Lấy phòng chiếu theo screen_id
        $screen = Screen::findOrFail($request->screen_id);

        // Kiểm tra số ghế hiện tại trong phòng chiếu
        $currentSeats = Seat::where('screen_id', $request->screen_id)->count();
        $totalSeats = $screen->total_seats;

        // Kiểm tra nếu số ghế ngồi đã đạt đến hoặc vượt quá tổng số ghế của phòng chiếu
        if ($currentSeats >= $totalSeats) {
            return redirect()->back()->with('error', 'Số ghế trong phòng chiếu đã đầy. Không thể thêm ghế mới.');
        }

        // Nếu chưa vượt quá, lưu ghế mới vào cơ sở dữ liệu
        Seat::create($request->all());  // Lưu ghế mới vào cơ sở dữ liệu

        return redirect()->route('admin.seats.index')->with('success', 'Seat created successfully.');
    }



    // Hiển thị form chỉnh sửa ghế
    public function edit($id)
    {
        $seat = Seat::findOrFail($id);  // Tìm ghế theo ID
        $screens = Screen::all();  // Lấy tất cả các phòng chiếu
        return view('admin.seats.edit', compact('seat', 'screens'));
    }

    // Cập nhật thông tin ghế
    public function update(Request $request, $id)
    {
        $seat = Seat::findOrFail($id);

        $request->validate([
            'row_name'   => 'required|string|min:1',
            'seat_number' => 'required|integer|min:1',
            'seat_type'  => 'required|in:Regular,VIP,Couple',
        ]);

        $seat->update($request->all());  // Cập nhật ghế

        return redirect()->route('admin.seats.index')->with('success', 'Seat updated successfully.');
    }

    // Xóa ghế
    public function destroy($id)
    {
        $seat = Seat::findOrFail($id);
        $seat->delete();  // Xóa ghế

        return redirect()->route('admin.seats.index')->with('success', 'Seat deleted successfully.');
    }
}
