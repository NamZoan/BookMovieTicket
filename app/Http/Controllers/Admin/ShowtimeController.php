<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Screen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{
    // Hiển thị danh sách suất chiếu
    public function index()
    {
        $showtimes = Showtime::with(['movie', 'screen.cinema'])
            ->orderBy('show_date', 'asc')
            ->orderBy('show_time', 'asc')
            ->get();
        return view('admin.showtimes.index', compact('showtimes'));
    }

    // Hiển thị form tạo suất chiếu mới
    public function create()
    {
        $movies = Movie::where('status', 'Now Showing')
            ->orWhere('status', 'Coming Soon')
            ->orderBy('title')
            ->get();
        $screens = Screen::with('cinema')->orderBy('screen_name')->get();
        return view('admin.showtimes.create', compact('movies', 'screens'));
    }

    // Lưu suất chiếu mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,movie_id',
            'screen_id' => 'required|exists:screens,screen_id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:show_time',
            'price_seat_normal' => 'required|numeric|min:10000|max:500000',
            'price_seat_vip' => 'required|numeric|min:10000|max:500000',
            'price_seat_couple' => 'required|numeric|min:10000|max:500000',
            'available_seats' => 'required|integer|min:1',
        ], [
            'movie_id.required' => 'Vui lòng chọn phim',
            'movie_id.exists' => 'Phim không tồn tại',
            'screen_id.required' => 'Vui lòng chọn phòng chiếu',
            'screen_id.exists' => 'Phòng chiếu không tồn tại',
            'show_date.required' => 'Vui lòng chọn ngày chiếu',
            'show_date.after_or_equal' => 'Ngày chiếu phải từ hôm nay trở đi',
            'show_time.required' => 'Vui lòng chọn giờ bắt đầu',
            'show_time.date_format' => 'Giờ bắt đầu không đúng định dạng',
            'end_time.required' => 'Vui lòng chọn giờ kết thúc',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'price_seat_normal.required' => 'Vui lòng nhập giá vé thường',
            'price_seat_vip.required' => 'Vui lòng nhập giá vé VIP',
            'price_seat_couple.required' => 'Vui lòng nhập giá vé cặp',
            'price_seat_normal.min' => 'Giá vé thường tối thiểu là 10,000 VNĐ',
            'price_seat_vip.min' => 'Giá vé VIP tối thiểu là 10,000 VNĐ',
            'price_seat_couple.min' => 'Giá vé cặp tối thiểu là 10,000 VNĐ',
            'available_seats.required' => 'Vui lòng nhập số ghế trống',
            'available_seats.integer' => 'Số ghế trống phải là số nguyên',
            'available_seats.min' => 'Số ghế trống phải lớn hơn 0',
        ]);

        // Kiểm tra logic giá vé
        if ($request->price_seat_vip <= $request->price_seat_normal) {
            return back()->withErrors(['price_seat_vip' => 'Giá vé VIP phải cao hơn giá vé thường'])->withInput();
        }

        if ($request->price_seat_couple <= $request->price_seat_vip) {
            return back()->withErrors(['price_seat_couple' => 'Giá vé cặp phải cao hơn giá vé VIP'])->withInput();
        }

        // Kiểm tra xung đột lịch chiếu
        $conflict = $this->checkScheduleConflict(
            $request->screen_id,
            $request->show_date,
            $request->show_time,
            $request->end_time
        );

        if ($conflict) {
            return back()->withErrors(['schedule_conflict' => 'Lịch chiếu này xung đột với lịch chiếu khác trong cùng phòng chiếu.'])->withInput();
        }

        // Kiểm tra số ghế trống không vượt quá tổng số ghế
        $screen = Screen::find($request->screen_id);
        if ($request->available_seats > $screen->total_seats) {
            return back()->withErrors(['available_seats' => 'Số ghế trống không được vượt quá tổng số ghế của phòng chiếu (' . $screen->total_seats . ' ghế).'])->withInput();
        }

        // Kiểm tra thời gian chiếu có hợp lý không (tối thiểu 30 phút, tối đa 4 giờ)
        $startTime = Carbon::parse($request->show_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = $endTime->diffInMinutes($startTime);

        if ($duration < 30) {
            return back()->withErrors(['end_time' => 'Thời gian chiếu phải ít nhất 30 phút.'])->withInput();
        }

        if ($duration > 240) {
            return back()->withErrors(['end_time' => 'Thời gian chiếu không được vượt quá 4 giờ.'])->withInput();
        }

        try {
            DB::beginTransaction();

            Showtime::create([
                'movie_id' => $request->movie_id,
                'screen_id' => $request->screen_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
                'end_time' => $request->end_time,
                'price_seat_normal' => $request->price_seat_normal,
                'price_seat_vip' => $request->price_seat_vip,
                'price_seat_couple' => $request->price_seat_couple,
                'available_seats' => $request->available_seats,
                'status' => 'Active'
            ]);

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Lịch chiếu đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo lịch chiếu. Vui lòng thử lại.'])->withInput();
        }
    }

    // Hiển thị form chỉnh sửa suất chiếu
    public function edit($id)
    {
        $showtime = Showtime::with(['movie', 'screen.cinema'])->findOrFail($id);
        $movies = Movie::where('status', 'Now Showing')
            ->orWhere('status', 'Coming Soon')
            ->orderBy('title')
            ->get();
        $screens = Screen::with('cinema')->orderBy('screen_name')->get();


        return view('admin.showtimes.edit', compact('showtime', 'movies', 'screens'));
    }

    // Cập nhật thông tin suất chiếu
    public function update(Request $request, $id)
    {
        $showtime = Showtime::findOrFail($id);

        $request->validate([
            'movie_id' => 'required|exists:movies,movie_id',
            'screen_id' => 'required|exists:screens,screen_id',
            'show_date' => 'required|date',
            'show_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:show_time',
            'price_seat_normal' => 'required|numeric|min:10000|max:500000',
            'price_seat_vip' => 'required|numeric|min:10000|max:500000',
            'price_seat_couple' => 'required|numeric|min:10000|max:500000',
            'available_seats' => 'required|integer|min:1',
        ], [
            'movie_id.required' => 'Vui lòng chọn phim',
            'movie_id.exists' => 'Phim không tồn tại',
            'screen_id.required' => 'Vui lòng chọn phòng chiếu',
            'screen_id.exists' => 'Phòng chiếu không tồn tại',
            'show_date.required' => 'Vui lòng chọn ngày chiếu',
            'show_time.required' => 'Vui lòng chọn giờ bắt đầu',
            'end_time.required' => 'Vui lòng chọn giờ kết thúc',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'price_seat_normal.required' => 'Vui lòng nhập giá vé thường',
            'price_seat_vip.required' => 'Vui lòng nhập giá vé VIP',
            'price_seat_couple.required' => 'Vui lòng nhập giá vé cặp',
        ]);

        // Kiểm tra logic giá vé
        if ($request->price_seat_vip <= $request->price_seat_normal) {
            return back()->withErrors(['price_seat_vip' => 'Giá vé VIP phải cao hơn giá vé thường'])->withInput();
        }

        if ($request->price_seat_couple <= $request->price_seat_vip) {
            return back()->withErrors(['price_seat_couple' => 'Giá vé cặp phải cao hơn giá vé VIP'])->withInput();
        }

        // Kiểm tra xung đột lịch chiếu (loại trừ lịch chiếu hiện tại)
        $conflict = $this->checkScheduleConflict(
            $request->screen_id,
            $request->show_date,
            $request->show_time,
            $request->end_time,
            $id
        );

        if ($conflict) {
            return back()->withErrors(['schedule_conflict' => 'Lịch chiếu này xung đột với lịch chiếu khác trong cùng phòng chiếu.'])->withInput();
        }

        // Kiểm tra số ghế trống không vượt quá tổng số ghế
        $screen = Screen::find($request->screen_id);
        if ($request->available_seats > $screen->total_seats) {
            return back()->withErrors(['available_seats' => 'Số ghế trống không được vượt quá tổng số ghế của phòng chiếu (' . $screen->total_seats . ' ghế).'])->withInput();
        }

        // Kiểm tra thời gian chiếu có hợp lý không (tối thiểu 30 phút, tối đa 4 giờ)
        $startTime = Carbon::parse($request->show_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = $endTime->diffInMinutes($startTime);

        if ($duration < 30) {
            return back()->withErrors(['end_time' => 'Thời gian chiếu phải ít nhất 30 phút.'])->withInput();
        }

        if ($duration > 240) {
            return back()->withErrors(['end_time' => 'Thời gian chiếu không được vượt quá 4 giờ.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $showtime->update([
                'movie_id' => $request->movie_id,
                'screen_id' => $request->screen_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
                'end_time' => $request->end_time,
                'price_seat_normal' => $request->price_seat_normal,
                'price_seat_vip' => $request->price_seat_vip,
                'price_seat_couple' => $request->price_seat_couple,
                'available_seats' => $request->available_seats,
            ]);

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Lịch chiếu đã được cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật lịch chiếu. Vui lòng thử lại.'])->withInput();
        }
    }

    // Xóa suất chiếu
    public function destroy($id)
    {
        $showtime = Showtime::findOrFail($id);

        // Kiểm tra xem lịch chiếu đã bắt đầu chưa
        $now = Carbon::now();
        $showDateTime = Carbon::parse($showtime->show_date . ' ' . $showtime->show_time);

        if ($now >= $showDateTime) {
            return redirect()->route('admin.showtimes.index')->with('error', 'Không thể xóa lịch chiếu đã bắt đầu hoặc đã kết thúc.');
        }

        try {
            DB::beginTransaction();
            $showtime->delete();
            DB::commit();
            return redirect()->route('admin.showtimes.index')->with('success', 'Lịch chiếu đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.showtimes.index')->with('error', 'Có lỗi xảy ra khi xóa lịch chiếu.');
        }
    }

    // Kiểm tra xung đột lịch chiếu
    private function checkScheduleConflict($screenId, $showDate, $showTime, $endTime, $excludeId = null)
    {
        $query = Showtime::where('screen_id', $screenId)
            ->where('show_date', $showDate)
            ->where(function ($q) use ($showTime, $endTime) {
                $q->where(function ($subQ) use ($showTime, $endTime) {
                    // Kiểm tra xem thời gian mới có chồng lấp với thời gian hiện có không
                    $subQ->where('show_time', '<', $endTime)
                         ->where('end_time', '>', $showTime);
                });
            });

        if ($excludeId) {
            $query->where('showtime_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    // API để lấy thông tin phòng chiếu
    public function getScreenInfo($screenId)
    {
        $screen = Screen::with('cinema')->find($screenId);
        if (!$screen) {
            return response()->json(['error' => 'Phòng chiếu không tồn tại'], 404);
        }
        return response()->json($screen);
    }

    // API để lấy thông tin phim
    public function getMovieInfo($movieId)
    {
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['error' => 'Phim không tồn tại'], 404);
        }
        return response()->json($movie);
    }

    // API để kiểm tra xung đột lịch chiếu
    public function checkConflict(Request $request)
    {
        $request->validate([
            'screen_id' => 'required|exists:screens,screen_id',
            'show_date' => 'required|date',
            'show_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:show_time',
            'exclude_id' => 'nullable|exists:showtimes,showtime_id'
        ]);

        $conflict = $this->checkScheduleConflict(
            $request->screen_id,
            $request->show_date,
            $request->show_time,
            $request->end_time,
            $request->exclude_id
        );

        return response()->json([
            'has_conflict' => $conflict,
            'message' => $conflict ? 'Lịch chiếu này xung đột với lịch chiếu khác' : 'Lịch chiếu hợp lệ'
        ]);
    }

    // API để lấy lịch chiếu theo phòng và ngày
    public function getShowtimesByScreenAndDate(Request $request)
    {
        $request->validate([
            'screen_id' => 'required|exists:screens,screen_id',
            'show_date' => 'required|date'
        ]);

        $showtimes = Showtime::where('screen_id', $request->screen_id)
            ->where('show_date', $request->show_date)
            ->with('movie')
            ->orderBy('show_time')
            ->get();

        return response()->json($showtimes);
    }
}
