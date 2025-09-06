<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\Cinema;
use Illuminate\Http\Request;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ScreenController extends Controller
{
    /**
     * Hiển thị danh sách phòng chiếu
     */
    public function index()
    {
        $screens = Screen::with(['cinema', 'seats'])
            ->withCount([
                'seats',
                'seats as normal_seats_count' => function ($query) {
                    $query->where('seat_type', 'Normal');
                },
                'seats as vip_seats_count' => function ($query) {
                    $query->where('seat_type', 'VIP');
                },
                'seats as couple_seats_count' => function ($query) {
                    $query->where('seat_type', 'Couple');
                },
                'seats as disabled_seats_count' => function ($query) {
                    $query->where('seat_type', 'Disabled');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.screens.index', compact('screens'));
    }

    /**
     * Hiển thị form tạo phòng chiếu mới
     */
    public function create()
    {
        $cinemas = Cinema::orderBy('name')->get();
        return view('admin.screens.create', compact('cinemas'));
    }

    /**
     * Lưu phòng chiếu mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'screen_name' => 'required|string|max:255',
            'cinema_id' => 'required|exists:cinemas,cinema_id',
            'seat_configuration' => 'required|json'
        ], [
            'screen_name.required' => 'Tên phòng chiếu không được để trống',
            'screen_name.max' => 'Tên phòng chiếu không được quá 255 ký tự',
            'screen_name.unique' => 'Tên phòng chiếu đã tồn tại',
            'cinema_id.required' => 'Vui lòng chọn rạp chiếu',
            'cinema_id.exists' => 'Rạp chiếu không tồn tại',
            'seat_configuration.required' => 'Vui lòng thiết kế ghế cho phòng chiếu',
            'seat_configuration.json' => 'Cấu hình ghế không hợp lệ'
        ]);

        try {
            DB::beginTransaction();

            $seatConfiguration = json_decode($request->seat_configuration, true);

            if (empty($seatConfiguration)) {
                throw new \Exception('Cấu hình ghế trống');
            }

            // Validate seat configuration structure
            $this->validateSeatConfiguration($seatConfiguration);

            $seatStats = $this->calculateSeatStatistics($seatConfiguration);
            // Tạo phòng chiếu mới
            $screen = Screen::create([
                'screen_name' => $request->screen_name,
                'cinema_id' => $request->cinema_id,
                'total_seats' => $seatStats['total'],
                'normal_seats' => $seatStats['normal'],
                'vip_seats' => $seatStats['vip'],
                'couple_seats' => $seatStats['couple'],
                'disabled_seats' => $seatStats['disabled'],
                'status' => 1, // Active by default
            ]);

            // Lưu cấu hình ghế
            $this->saveSeatConfiguration($screen->screen_id, $seatConfiguration);

            DB::commit();

            return redirect()
                ->route('admin.screens.index')
                ->with('success', "Tạo phòng chiếu '{$screen->screen_name}' thành công với {$seatStats['total']} ghế");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating screen: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa phòng chiếu
     */
    public function edit($id)
    {
        try {
            $screen = Screen::with(['cinema', 'seats' => function ($query) {
                $query->orderBy('row_name')->orderBy('seat_number');
            }])->findOrFail($id);

            $cinemas = Cinema::orderBy('name')->get();

            // Build seat configuration for JavaScript từ database
            $screen->seat_configuration = $this->buildSeatConfigurationFromDatabase($screen->seats);

            // Calculate current statistics từ database thực tế
            $screen->normal_seats = $screen->seats->where('seat_type', 'Normal')->count();
            $screen->vip_seats = $screen->seats->where('seat_type', 'VIP')->count();
            $screen->couple_seats = $screen->seats->where('seat_type', 'Couple')->count();
            $screen->disabled_seats = $screen->seats->where('seat_type', 'Disabled')->count();
            $screen->total_seats = $screen->seats->count();

            return view('admin.screens.edit', compact('screen', 'cinemas'));

        } catch (\Exception $e) {
            Log::error('Error loading screen for edit: ' . $e->getMessage());

            return redirect()
                ->route('admin.screens.index')
                ->with('error', 'Không tìm thấy phòng chiếu hoặc có lỗi xảy ra');
        }
    }

    /**
     * Cập nhật thông tin phòng chiếu
     */
    public function update(Request $request, $id)
    {
        $screen = Screen::findOrFail($id);

        // Validation với unique rule exclude current screen
        $request->validate([
            'screen_name' => 'required|string|max:255' . $screen->screen_id . ',screen_id',
            'cinema_id' => 'required|exists:cinemas,cinema_id',
            'seat_configuration' => 'required|json'
        ], [
            'screen_name.required' => 'Tên phòng chiếu không được để trống',
            'screen_name.max' => 'Tên phòng chiếu không được quá 255 ký tự',
            'cinema_id.required' => 'Vui lòng chọn rạp chiếu',
            'cinema_id.exists' => 'Rạp chiếu không tồn tại',
            'seat_configuration.required' => 'Vui lòng thiết kế ghế cho phòng chiếu',
            'seat_configuration.json' => 'Cấu hình ghế không hợp lệ'
        ]);



        try {
            DB::beginTransaction();

            $seatConfiguration = json_decode($request->seat_configuration, true);
            if (empty($seatConfiguration)) {
                throw new \Exception('Cấu hình ghế trống');
            }

            // Validate seat configuration structure
            $this->validateSeatConfiguration($seatConfiguration);

            $seatStats = $this->calculateSeatStatistics($seatConfiguration);

            // Kiểm tra xem có thay đổi cinema_id không
            $cinemaChanged = $screen->cinema_id != $request->cinema_id;

            // Cập nhật thông tin phòng chiếu
            $screen->update([
                'screen_name' => $request->screen_name,
                'cinema_id' => $request->cinema_id,
                'total_seats' => $seatStats['total'],
                'normal_seats' => $seatStats['normal'],
                'vip_seats' => $seatStats['vip'],
                'couple_seats' => $seatStats['couple'],
                'disabled_seats' => $seatStats['disabled'],
                'updated_at' => now(),
            ]);

            // Xóa tất cả ghế cũ và tạo lại với cấu hình mới
            $screen->seats()->delete();
            $this->saveSeatConfiguration($screen->screen_id, $seatConfiguration);

            DB::commit();

            $successMessage = "Cập nhật phòng chiếu '{$screen->screen_name}' thành công với {$seatStats['total']} ghế";

            if ($cinemaChanged) {
                $successMessage .= " và đã chuyển sang rạp mới";
            }

            return redirect()
                ->route('admin.screens.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating screen: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa phòng chiếu
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $screen = Screen::with(['seats', 'showtimes'])->findOrFail($id);

            // Check if screen has any active showtimes
            $hasActiveShowtimes = $screen->showtimes()
                ->where('showtime_date', '>=', now()->format('Y-m-d'))
                ->exists();

            if ($hasActiveShowtimes) {
                throw new \Exception('Không thể xóa phòng chiếu có lịch chiếu đang hoạt động');
            }

            // Check if there are any bookings for this screen
            $hasBookings = DB::table('bookings')
                ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.showtime_id')
                ->where('showtimes.screen_id', $screen->screen_id)
                ->where('bookings.status', '!=', 'cancelled')
                ->exists();

            if ($hasBookings) {
                throw new \Exception('Không thể xóa phòng chiếu có vé đã được đặt');
            }

            // Delete all seats first
            $screen->seats()->delete();

            // Delete screen
            $screenName = $screen->screen_name;
            $screen->delete();

            DB::commit();

            return redirect()
                ->route('admin.screens.index')
                ->with('success', "Xóa phòng chiếu '{$screenName}' thành công");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting screen: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết phòng chiếu
     */
    public function show($id)
    {
        try {
            $screen = Screen::with(['cinema', 'seats' => function ($query) {
                $query->orderBy('row_name')->orderBy('seat_number');
            }])->findOrFail($id);

            // Build seat map for display
            $seatMap = $this->buildSeatMapForDisplay($screen->seats);

            // Calculate statistics
            $statistics = [
                'total_seats' => $screen->seats->count(),
                'normal_seats' => $screen->seats->where('seat_type', 'Normal')->count(),
                'vip_seats' => $screen->seats->where('seat_type', 'VIP')->count(),
                'couple_seats' => $screen->seats->where('seat_type', 'Couple')->count(),
                'disabled_seats' => $screen->seats->where('seat_type', 'Disabled')->count(),
                'rows_count' => $screen->seats->pluck('row_name')->unique()->count(),
            ];

            return view('admin.screens.show', compact('screen', 'seatMap', 'statistics'));

        } catch (\Exception $e) {
            Log::error('Error showing screen: ' . $e->getMessage());

            return redirect()
                ->route('admin.screens.index')
                ->with('error', 'Không tìm thấy phòng chiếu hoặc có lỗi xảy ra');
        }
    }

    /**
     * Lưu cấu hình ghế vào database
     */
    private function saveSeatConfiguration($screenId, $seatConfiguration)
    {
        $seatsToInsert = [];

        foreach ($seatConfiguration as $rowData) {
            $rowName = $rowData['row'];

            foreach ($rowData['seats'] as $seatData) {
                // Extract seat number từ seat ID (e.g., A1 -> 1, B12 -> 12)
                $seatNumber = preg_replace('/^[A-Z]+/', '', $seatData['id']);

                // Validate seat type - map frontend values to database enum values
                $seatType = $this->mapSeatTypeToDatabase($seatData['type']);

                $seatsToInsert[] = [
                    'screen_id' => $screenId,
                    'row_name' => $rowName,
                    'seat_number' => (int)$seatNumber,
                    'seat_type' => $seatType,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Bulk insert for better performance
        if (!empty($seatsToInsert)) {
            Seat::insert($seatsToInsert);
        }
    }

    /**
     * Map frontend seat type to database enum value
     */
    private function mapSeatTypeToDatabase($frontendType)
    {
        $mapping = [
            'Normal' => 'Normal',
            'VIP' => 'VIP',
            'Couple' => 'Couple',
            'Disabled' => 'Disabled'
        ];

        return isset($mapping[$frontendType]) ? $mapping[$frontendType] : 'Normal';
    }

    /**
     * Map database seat type to frontend value
     */
    private function mapSeatTypeToFrontend($databaseType)
    {
        $mapping = [
            'Normal' => 'Normal',
            'VIP' => 'VIP',
            'Couple' => 'Couple',
            'Disabled' => 'Disabled'
        ];

        return isset($mapping[$databaseType]) ? $mapping[$databaseType] : 'Normal';
    }

    /**
     * Validate seat configuration structure từ frontend
     */
    private function validateSeatConfiguration($seatConfiguration)
    {
        if (!is_array($seatConfiguration)) {
            throw new \Exception('Cấu hình ghế phải là một mảng');
        }

        if (empty($seatConfiguration)) {
            throw new \Exception('Phải có ít nhất một hàng ghế');
        }

        $validTypes = ['Normal', 'VIP', 'Couple', 'Disabled'];
        $usedSeatIds = [];
        $validRowPattern = '/^[A-Z]$/'; // Chỉ chấp nhận 1 ký tự A-Z

        foreach ($seatConfiguration as $rowIndex => $rowData) {
            if (!isset($rowData['row']) || !isset($rowData['seats'])) {
                throw new \Exception("Dữ liệu hàng {$rowIndex} không hợp lệ (thiếu row hoặc seats)");
            }

            // Validate row name format
            if (!preg_match($validRowPattern, $rowData['row'])) {
                throw new \Exception("Tên hàng '{$rowData['row']}' không hợp lệ. Chỉ chấp nhận A-Z");
            }

            if (!is_array($rowData['seats'])) {
                throw new \Exception("Dữ liệu ghế trong hàng {$rowData['row']} phải là mảng");
            }

            if (empty($rowData['seats'])) {
                throw new \Exception("Hàng {$rowData['row']} phải có ít nhất một ghế");
            }

            foreach ($rowData['seats'] as $seatIndex => $seatData) {
                // Validate seat data structure
                if (!isset($seatData['id']) || !isset($seatData['type'])) {
                    throw new \Exception("Dữ liệu ghế {$seatIndex} trong hàng {$rowData['row']} không hợp lệ (thiếu id hoặc type)");
                }

                // Validate seat type
                if (!in_array($seatData['type'], $validTypes)) {
                    throw new \Exception("Loại ghế '{$seatData['type']}' không hợp lệ. Chỉ chấp nhận: " . implode(', ', $validTypes));
                }

                // Validate seat ID format (e.g., A1, B10, C5)
                if (!preg_match('/^[A-Z]\d{1,2}$/', $seatData['id'])) {
                    throw new \Exception("ID ghế '{$seatData['id']}' không hợp lệ. Định dạng phải là chữ cái + số (VD: A1, B10)");
                }

                // Check for duplicate seat IDs
                if (in_array($seatData['id'], $usedSeatIds)) {
                    throw new \Exception("ID ghế '{$seatData['id']}' bị trùng lặp");
                }

                $usedSeatIds[] = $seatData['id'];
            }
        }

        // Validate total seat count
        $totalSeats = array_sum(array_map(function($row) { return count($row['seats']); }, $seatConfiguration));
        if ($totalSeats > 500) { // Giới hạn tối đa
            throw new \Exception("Tổng số ghế ({$totalSeats}) vượt quá giới hạn cho phép (500 ghế)");
        }

        if ($totalSeats < 1) {
            throw new \Exception("Phải có ít nhất 1 ghế");
        }
    }

    /**
     * Tính thống kê ghế từ configuration
     */
    private function calculateSeatStatistics($seatConfiguration)
    {
        $stats = [
            'total' => 0,
            'normal' => 0,
            'vip' => 0,
            'couple' => 0,
            'disabled' => 0
        ];

        foreach ($seatConfiguration as $rowData) {
            foreach ($rowData['seats'] as $seatData) {
                $stats['total']++;

                switch ($seatData['type']) {
                    case 'Normal':
                        $stats['normal']++;
                        break;
                    case 'VIP':
                        $stats['vip']++;
                        break;
                    case 'Couple':
                        $stats['couple']++;
                        break;
                    case 'Disabled':
                        $stats['disabled']++;
                        break;
                    default:
                        $stats['normal']++; // Fallback to normal
                        break;
                }
            }
        }

        return $stats;
    }

    /**
     * Tạo seat configuration từ database seats (cho edit form)
     * Hàm này được tối ưu để phù hợp với cấu trúc frontend
     */
    private function buildSeatConfigurationFromDatabase($seats)
    {
        if ($seats->isEmpty()) {
            return [];
        }

        $configuration = [];
        $groupedSeats = $seats->groupBy('row_name')->sortKeys();

        foreach ($groupedSeats as $rowName => $rowSeats) {
            $seatData = [
                'row' => $rowName,
                'seats' => []
            ];

            // Sort seats by seat_number
            $sortedSeats = $rowSeats->sortBy('seat_number');

            foreach ($sortedSeats as $seat) {
                $seatData['seats'][] = [
                    'id' => $rowName . $seat->seat_number,
                    'type' => $this->mapSeatTypeToFrontend($seat->seat_type),
                    'seat_number' => $seat->seat_number,
                ];
            }

            // Chỉ thêm hàng có ghế
            if (!empty($seatData['seats'])) {
                $configuration[] = $seatData;
            }
        }

        return $configuration;
    }

    /**
     * Build seat map for display purposes (cho show view)
     */
    private function buildSeatMapForDisplay($seats)
    {
        $seatMap = [];
        $groupedSeats = $seats->groupBy('row_name')->sortKeys();

        foreach ($groupedSeats as $rowName => $rowSeats) {
            $seatMap[$rowName] = $rowSeats->sortBy('seat_number')->map(function ($seat) use ($rowName) {
                return [
                    'id' => $rowName . $seat->seat_number,
                    'seat_id' => $seat->seat_id,
                    'row' => $seat->row_name,
                    'number' => $seat->seat_number,
                    'type' => $seat->seat_type,
                    'display_name' => $seat->seat_type === 'Couple' ? 'ĐÔI' : $rowName . $seat->seat_number
                ];
            })->toArray();
        }

        return $seatMap;
    }

    /**
     * API endpoint - Lấy thông tin seat map cho frontend
     */
    public function getSeatMap(Screen $screen)
    {
        try {
            $seats = $screen->seats()
                ->orderBy('row_name')
                ->orderBy('seat_number')
                ->get();

            $seatMap = $this->buildSeatMapForDisplay($seats);
            $statistics = $this->calculateCurrentStatistics($seats);

            return response()->json([
                'success' => true,
                'data' => [
                    'screen_id' => $screen->screen_id,
                    'screen_name' => $screen->screen_name,
                    'cinema_name' => $screen->cinema->name,
                    'seat_map' => $seatMap,
                    'statistics' => $statistics,
                    'seat_configuration' => $this->buildSeatConfigurationFromDatabase($seats)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting seat map: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin ghế'
            ], 500);
        }
    }

    /**
     * Calculate current statistics from seats collection
     */
    private function calculateCurrentStatistics($seats)
    {
        return [
            'total_seats' => $seats->count(),
            'normal_seats' => $seats->where('seat_type', 'Normal')->count(),
            'vip_seats' => $seats->where('seat_type', 'VIP')->count(),
            'couple_seats' => $seats->where('seat_type', 'Couple')->count(),
            'disabled_seats' => $seats->where('seat_type', 'Disabled')->count(),
            'rows_count' => $seats->pluck('row_name')->unique()->count(),
        ];
    }

    /**
     * Duplicate screen (sao chép layout ghế)
     */
    public function duplicate($id)
    {
        try {
            DB::beginTransaction();

            $originalScreen = Screen::with('seats')->findOrFail($id);

            // Tạo tên mới không trùng
            $baseName = $originalScreen->screen_name;
            $copyNumber = 1;
            $newName = $baseName . ' (Sao chép)';

            // Kiểm tra tên trùng và tạo tên unique
            while (Screen::where('screen_name', $newName)->exists()) {
                $copyNumber++;
                $newName = $baseName . " (Sao chép {$copyNumber})";
            }

            // Create new screen
            $newScreen = Screen::create([
                'screen_name' => $newName,
                'cinema_id' => $originalScreen->cinema_id,
                'total_seats' => $originalScreen->total_seats,
                'normal_seats' => $originalScreen->normal_seats,
                'vip_seats' => $originalScreen->vip_seats,
                'couple_seats' => $originalScreen->couple_seats,
                'disabled_seats' => $originalScreen->disabled_seats,
                'status' => 0, // Inactive để admin review
            ]);

            // Copy all seats với
            $seatsToInsert = [];
            foreach ($originalScreen->seats as $seat) {
                $seatsToInsert[] = [
                    'screen_id' => $newScreen->screen_id,
                    'row_name' => $seat->row_name,
                    'seat_number' => $seat->seat_number,
                    'seat_type' => $seat->seat_type,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($seatsToInsert)) {
                Seat::insert($seatsToInsert);
            }

            DB::commit();

            return redirect()
                ->route('admin.screens.edit', $newScreen->screen_id)
                ->with('success', "Sao chép phòng chiếu thành công. Hãy kiểm tra và chỉnh sửa '{$newScreen->screen_name}' nếu cần.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error duplicating screen: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi sao chép phòng chiếu: ' . $e->getMessage());
        }
    }

    /**
     * Toggle screen status (active/inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $screen = Screen::findOrFail($id);

            // Nếu đang deactivate, kiểm tra có showtime active không
            if ($screen->status == 1) {
                $hasActiveShowtimes = $screen->showtimes()
                    ->where('showtime_date', '>=', now()->format('Y-m-d'))
                    ->exists();

                if ($hasActiveShowtimes) {
                    throw new \Exception('Không thể vô hiệu hóa phòng chiếu có lịch chiếu đang hoạt động');
                }
            }

            $newStatus = $screen->status ? 0 : 1;
            $screen->update(['status' => $newStatus]);

            $statusText = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';

            return redirect()
                ->back()
                ->with('success', "Đã {$statusText} phòng chiếu '{$screen->screen_name}'");

        } catch (\Exception $e) {
            Log::error('Error toggling screen status: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Bulk operations for multiple screens
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'screen_ids' => 'required|array|min:1',
            'screen_ids.*' => 'exists:screens,screen_id'
        ], [
            'action.required' => 'Vui lòng chọn thao tác',
            'action.in' => 'Thao tác không hợp lệ',
            'screen_ids.required' => 'Vui lòng chọn ít nhất một phòng chiếu',
            'screen_ids.min' => 'Vui lòng chọn ít nhất một phòng chiếu',
            'screen_ids.*.exists' => 'Một số phòng chiếu được chọn không tồn tại'
        ]);

        try {
            DB::beginTransaction();

            $screenIds = $request->screen_ids;
            $action = $request->action;

            switch ($action) {
                case 'activate':
                    Screen::whereIn('screen_id', $screenIds)->update(['status' => 1]);
                    $message = 'Kích hoạt ' . count($screenIds) . ' phòng chiếu thành công';
                    break;

                case 'deactivate':
                    // Check for active showtimes
                    $screensWithShowtimes = Screen::whereIn('screen_id', $screenIds)
                        ->whereHas('showtimes', function ($query) {
                            $query->where('showtime_date', '>=', now()->format('Y-m-d'));
                        })
                        ->pluck('screen_name')
                        ->toArray();

                    if (!empty($screensWithShowtimes)) {
                        throw new \Exception('Không thể vô hiệu hóa các phòng chiếu có lịch chiếu: ' . implode(', ', $screensWithShowtimes));
                    }

                    Screen::whereIn('screen_id', $screenIds)->update(['status' => 0]);
                    $message = 'Vô hiệu hóa ' . count($screenIds) . ' phòng chiếu thành công';
                    break;

                case 'delete':
                    // Check for active showtimes
                    $screensWithShowtimes = Screen::whereIn('screen_id', $screenIds)
                        ->whereHas('showtimes', function ($query) {
                            $query->where('showtime_date', '>=', now()->format('Y-m-d'));
                        })
                        ->pluck('screen_name')
                        ->toArray();

                    if (!empty($screensWithShowtimes)) {
                        throw new \Exception('Không thể xóa các phòng chiếu có lịch chiếu: ' . implode(', ', $screensWithShowtimes));
                    }

                    // Check for existing bookings
                    $screensWithBookings = Screen::whereIn('screen_id', $screenIds)
                        ->whereHas('showtimes.bookings', function ($query) {
                            $query->where('status', '!=', 'cancelled');
                        })
                        ->pluck('screen_name')
                        ->toArray();

                    if (!empty($screensWithBookings)) {
                        throw new \Exception('Không thể xóa các phòng chiếu có vé đã đặt: ' . implode(', ', $screensWithBookings));
                    }

                    // Delete seats first, then screens
                    Seat::whereIn('screen_id', $screenIds)->delete();
                    Screen::whereIn('screen_id', $screenIds)->delete();
                    $message = 'Xóa ' . count($screenIds) . ' phòng chiếu thành công';
                    break;
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in bulk action: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Export screen layout as JSON
     */
    public function exportLayout($id)
    {
        try {
            $screen = Screen::with(['cinema', 'seats'])->findOrFail($id);
            $configuration = $this->buildSeatConfigurationFromDatabase($screen->seats);

            $exportData = [
                'screen_name' => $screen->screen_name,
                'cinema_name' => $screen->cinema->name,
                'exported_at' => now()->toISOString(),
                'total_seats' => $screen->seats->count(),
                'statistics' => [
                    'normal_seats' => $screen->seats->where('seat_type', 'Normal')->count(),
                    'vip_seats' => $screen->seats->where('seat_type', 'VIP')->count(),
                    'couple_seats' => $screen->seats->where('seat_type', 'Couple')->count(),
                    'disabled_seats' => $screen->seats->where('seat_type', 'Disabled')->count(),
                    'rows_count' => $screen->seats->pluck('row_name')->unique()->count()
                ],
                'seat_configuration' => $configuration,
                'version' => '1.0' // Version cho tương lai nếu cần migrate
            ];

            $filename = 'screen_layout_' . $screen->screen_id . '_' . now()->format('Y_m_d_H_i_s') . '.json';

            return response()
                ->json($exportData, 200, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]);

        } catch (\Exception $e) {
            Log::error('Error exporting screen layout: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi xuất layout phòng chiếu');
        }
    }

    /**
     * Import screen layout from JSON
     */
    public function importLayout(Request $request, $id)
    {
        $request->validate([
            'layout_file' => 'required|file|mimes:json|max:2048' // Max 2MB
        ], [
            'layout_file.required' => 'Vui lòng chọn file layout',
            'layout_file.mimes' => 'File phải có định dạng JSON',
            'layout_file.max' => 'File không được vượt quá 2MB'
        ]);

        try {
            DB::beginTransaction();

            $screen = Screen::findOrFail($id);
            $fileContent = file_get_contents($request->file('layout_file')->getRealPath());
            $importData = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('File JSON không hợp lệ: ' . json_last_error_msg());
            }

            if (!$importData || !isset($importData['seat_configuration'])) {
                throw new \Exception('File không hợp lệ hoặc thiếu cấu hình ghế');
            }

            $seatConfiguration = $importData['seat_configuration'];
            $this->validateSeatConfiguration($seatConfiguration);

            $seatStats = $this->calculateSeatStatistics($seatConfiguration);

            // Delete old seats and save new configuration
            $screen->seats()->delete();
            $this->saveSeatConfiguration($screen->screen_id, $seatConfiguration);

            // Update screen statistics
            $screen->update([
                'total_seats' => $seatStats['total'],
                'normal_seats' => $seatStats['normal'],
                'vip_seats' => $seatStats['vip'],
                'couple_seats' => $seatStats['couple'],
                'disabled_seats' => $seatStats['disabled'],
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.screens.edit', $screen->screen_id)
                ->with('success', "Import layout thành công với {$seatStats['total']} ghế");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error importing screen layout: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi import layout: ' . $e->getMessage());
        }
    }

    /**
     * Get available screens for a cinema (API endpoint cho frontend)
     */
    public function getAvailableScreens($cinemaId)
    {
        try {
            $screens = Screen::where('cinema_id', $cinemaId)
                ->where('status', 1)
                ->orderBy('screen_name')
                ->get(['screen_id', 'screen_name', 'total_seats', 'normal_seats', 'vip_seats', 'couple_seats']);

            return response()->json([
                'success' => true,
                'data' => $screens
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available screens: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách phòng chiếu'
            ], 500);
        }
    }

    /**
     * Validate seat availability for booking (API endpoint)
     */
    public function validateSeatAvailability(Request $request, $screenId)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,showtime_id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'string'
        ]);

        try {
            $screen = Screen::findOrFail($screenId);
            $seatIds = $request->seat_ids;
            $showtimeId = $request->showtime_id;

            // Validate seat IDs exist in this screen
            $validSeats = $screen->seats()
                ->whereIn(DB::raw("CONCAT(row_name, seat_number)"), $seatIds)
                ->where('seat_type', '!=', 'Disabled')
                ->pluck(DB::raw("CONCAT(row_name, seat_number)"))
                ->toArray();

            $invalidSeats = array_diff($seatIds, $validSeats);

            // Check if seats are already booked for this showtime
            $bookedSeats = DB::table('bookings')
                ->join('booking_seats', 'bookings.booking_id', '=', 'booking_seats.booking_id')
                ->join('seats', 'booking_seats.seat_id', '=', 'seats.seat_id')
                ->where('bookings.showtime_id', $showtimeId)
                ->where('bookings.status', '!=', 'cancelled')
                ->whereIn(DB::raw("CONCAT(seats.row_name, seats.seat_number)"), $seatIds)
                ->pluck(DB::raw("CONCAT(seats.row_name, seats.seat_number)"))
                ->toArray();

            $unavailableSeats = array_merge($invalidSeats, $bookedSeats);

            return response()->json([
                'success' => true,
                'data' => [
                    'available' => empty($unavailableSeats),
                    'unavailable_seats' => $unavailableSeats,
                    'invalid_seats' => $invalidSeats,
                    'booked_seats' => $bookedSeats,
                    'total_requested' => count($seatIds),
                    'total_available' => count($seatIds) - count($unavailableSeats)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating seat availability: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra ghế'
            ], 500);
        }
    }

    /**
     * Get seat pricing for a specific screen and showtime
     */
    public function getSeatPricing($screenId, $showtimeId = null)
    {
        try {
            $screen = Screen::with('cinema')->findOrFail($screenId);

            // Base pricing (có thể lấy từ cinema hoặc system settings)
            $basePricing = [
                'Normal' => 80000, // 80k VND
                'VIP' => 120000,   // 120k VND
                'Couple' => 200000 // 200k VND
            ];

            // Nếu có showtime, có thể áp dụng pricing đặc biệt
            $pricing = $basePricing;
            if ($showtimeId) {
                // Logic để điều chỉnh giá theo showtime (giờ chiếu, ngày đặc biệt, etc.)
                $showtime = DB::table('showtimes')->find($showtimeId);
                if ($showtime) {
                    // Ví dụ: tăng giá 20% cho suất chiếu cuối tuần
                    $isWeekend = in_array(date('w', strtotime($showtime->showtime_date)), [0, 6]);
                    if ($isWeekend) {
                        foreach ($pricing as $type => $price) {
                            $pricing[$type] = $price * 1.2;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'screen_id' => $screen->screen_id,
                    'cinema_name' => $screen->cinema->name,
                    'pricing' => $pricing,
                    'currency' => 'VND'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting seat pricing: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin giá ghế'
            ], 500);
        }
    }

    /**
     * Preview seat layout (dành cho testing layout trước khi save)
     */
    public function previewLayout(Request $request)
    {
        $request->validate([
            'seat_configuration' => 'required|json'
        ]);

        try {
            $seatConfiguration = json_decode($request->seat_configuration, true);
            $this->validateSeatConfiguration($seatConfiguration);
            $statistics = $this->calculateSeatStatistics($seatConfiguration);

            return response()->json([
                'success' => true,
                'data' => [
                    'configuration' => $seatConfiguration,
                    'statistics' => $statistics,
                    'preview_url' => null // Có thể implement preview URL nếu cần
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get screen templates (mẫu thiết kế có sẵn)
     */
    public function getTemplates()
    {
        $templates = [
            'small' => [
                'name' => 'Phòng nhỏ (50 ghế)',
                'description' => '5 hàng x 10 ghế, có ghế VIP ở giữa',
                'total_seats' => 50,
                'configuration' => $this->generateSmallTemplate()
            ],
            'medium' => [
                'name' => 'Phòng trung bình (80 ghế)',
                'description' => '8 hàng với số ghế khác nhau, khu VIP và ghế đôi',
                'total_seats' => 80,
                'configuration' => $this->generateMediumTemplate()
            ],
            'large' => [
                'name' => 'Phòng lớn (100 ghế)',
                'description' => '8 hàng x 12-14 ghế, khu VIP rộng',
                'total_seats' => 100,
                'configuration' => $this->generateLargeTemplate()
            ],
            'premium' => [
                'name' => 'Phòng premium (60 ghế)',
                'description' => 'Toàn bộ ghế VIP và ghế đôi, thiết kế cao cấp',
                'total_seats' => 60,
                'configuration' => $this->generatePremiumTemplate()
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    /**
     * Apply template to screen
     */
    public function applyTemplate(Request $request, $id)
    {
        $request->validate([
            'template' => 'required|in:small,medium,large,premium'
        ]);

        try {
            $templates = [
                'small' => $this->generateSmallTemplate(),
                'medium' => $this->generateMediumTemplate(),
                'large' => $this->generateLargeTemplate(),
                'premium' => $this->generatePremiumTemplate()
            ];

            $templateConfig = $templates[$request->template];
            $statistics = $this->calculateSeatStatistics($templateConfig);

            return response()->json([
                'success' => true,
                'data' => [
                    'configuration' => $templateConfig,
                    'statistics' => $statistics
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error applying template: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng mẫu thiết kế'
            ], 500);
        }
    }

    /**
     * Generate small template configuration
     */
    private function generateSmallTemplate()
    {
        $configuration = [];

        for ($row = 0; $row < 5; $row++) {
            $rowLetter = chr(65 + $row); // A, B, C, D, E
            $rowData = [
                'row' => $rowLetter,
                'seats' => []
            ];

            for ($seat = 1; $seat <= 10; $seat++) {
                $seatType = 'Normal';

                // Hàng C (giữa) có ghế VIP ở vị trí 4-7
                if ($rowLetter === 'C' && $seat >= 4 && $seat <= 7) {
                    $seatType = 'VIP';
                }

                $rowData['seats'][] = [
                    'id' => $rowLetter . $seat,
                    'type' => $seatType,
                ];
            }

            $configuration[] = $rowData;
        }

        return $configuration;
    }

    /**
     * Generate medium template configuration
     */
    private function generateMediumTemplate()
    {
        $configuration = [];
        $seatCounts = [8, 10, 10, 12, 12, 10, 10, 8]; // Tổng: 80 ghế

        for ($row = 0; $row < 8; $row++) {
            $rowLetter = chr(65 + $row);
            $seatCount = $seatCounts[$row];
            $rowData = [
                'row' => $rowLetter,
                'seats' => []
            ];

            for ($seat = 1; $seat <= $seatCount; $seat++) {
                $seatType = 'Normal';

                // Hàng D, E có ghế VIP ở giữa
                if (in_array($rowLetter, ['D', 'E']) && $seat >= 4 && $seat <= 9) {
                    $seatType = 'VIP';
                }

                // Hàng cuối có một số ghế đôi
                if ($rowLetter === 'H' && $seat >= 3 && $seat <= 6 && $seat % 2 === 1) {
                    $seatType = 'Couple';
                }

                $rowData['seats'][] = [
                    'id' => $rowLetter . $seat,
                    'type' => $seatType,
                ];
            }

            $configuration[] = $rowData;
        }

        return $configuration;
    }

    /**
     * Generate large template configuration
     */
    private function generateLargeTemplate()
    {
        $configuration = [];
        $seatCounts = [12, 14, 14, 14, 14, 14, 14, 12]; // Tổng: 108 ghế

        for ($row = 0; $row < 8; $row++) {
            $rowLetter = chr(65 + $row);
            $seatCount = $seatCounts[$row];
            $rowData = [
                'row' => $rowLetter,
                'seats' => []
            ];

            for ($seat = 1; $seat <= $seatCount; $seat++) {
                $seatType = 'Normal';

                // Hàng D, E có ghế VIP rộng hơn
                if (in_array($rowLetter, ['D', 'E']) && $seat >= 4 && $seat <= 11) {
                    $seatType = 'VIP';
                }

                $rowData['seats'][] = [
                    'id' => $rowLetter . $seat,
                    'type' => $seatType,
                ];
            }

            $configuration[] = $rowData;
        }

        return $configuration;
    }

    /**
     * Generate premium template configuration
     */
    private function generatePremiumTemplate()
    {
        $configuration = [];
        $seatCounts = [10, 10, 8, 8, 8, 8]; // 6 hàng, tổng 52 ghế

        for ($row = 0; $row < 6; $row++) {
            $rowLetter = chr(65 + $row);
            $seatCount = $seatCounts[$row];
            $rowData = [
                'row' => $rowLetter,
                'seats' => []
            ];

            for ($seat = 1; $seat <= $seatCount; $seat++) {
                $seatType = 'VIP'; // Mặc định là VIP

                // Hàng cuối có ghế đôi
                if ($row >= 4 && $seat % 2 === 1 && $seat < $seatCount) {
                    $seatType = 'Couple';
                }

                $rowData['seats'][] = [
                    'id' => $rowLetter . $seat,
                    'type' => $seatType,
                ];
            }

            $configuration[] = $rowData;
        }

        return $configuration;
    }

    /**
     * Get screen statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_screens' => Screen::count(),
                'active_screens' => Screen::where('status', 1)->count(),
                'inactive_screens' => Screen::where('status', 0)->count(),
                'total_seats' => Screen::sum('total_seats'),
                'by_cinema' => Cinema::withCount(['screens' => function($query) {
                    $query->where('status', 1);
                }])->get(['cinema_id', 'name', 'screens_count']),
                'seat_types' => [
                    'normal' => Screen::sum('normal_seats'),
                    'vip' => Screen::sum('vip_seats'),
                    'couple' => Screen::sum('couple_seats'),
                    'disabled' => Screen::sum('disabled_seats')
                ],
                'largest_screen' => Screen::orderBy('total_seats', 'desc')->first(['screen_name', 'total_seats']),
                'newest_screen' => Screen::latest()->first(['screen_name', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting screen statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê'
            ], 500);
        }
    }

    /**
     * Search screens (cho autocomplete, select2, etc.)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $cinemaId = $request->get('cinema_id');
        $limit = min($request->get('limit', 20), 50); // Giới hạn tối đa 50

        try {
            $screensQuery = Screen::with('cinema')
                ->where('screen_name', 'like', "%{$query}%")
                ->where('status', 1);

            if ($cinemaId) {
                $screensQuery->where('cinema_id', $cinemaId);
            }

            $screens = $screensQuery
                ->orderBy('screen_name')
                ->limit($limit)
                ->get(['screen_id', 'screen_name', 'cinema_id', 'total_seats']);

            return response()->json([
                'success' => true,
                'data' => $screens->map(function($screen) {
                    return [
                        'id' => $screen->screen_id,
                        'text' => $screen->screen_name . " ({$screen->cinema->name}) - {$screen->total_seats} ghế",
                        'screen_name' => $screen->screen_name,
                        'cinema_name' => $screen->cinema->name,
                        'total_seats' => $screen->total_seats
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching screens: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
    }

    /**
     * Check screen name availability (AJAX)
     */
    public function checkNameAvailability(Request $request)
    {
        $screenName = $request->get('screen_name');
        $excludeId = $request->get('exclude_id'); // Cho trường hợp edit

        $query = Screen::where('screen_name', $screenName);

        if ($excludeId) {
            $query->where('screen_id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Tên phòng chiếu đã tồn tại' : 'Tên phòng chiếu có thể sử dụng'
        ]);
    }

    /**
     * Refresh seat configuration (sync database với actual seat data)
     */
    public function refreshSeatConfiguration($id)
    {
        try {
            DB::beginTransaction();

            $screen = Screen::with('seats')->findOrFail($id);

            // Recalculate statistics từ database thực tế
            $actualStats = $this->calculateCurrentStatistics($screen->seats);

            // Update screen với số liệu thực tế
            $screen->update([
                'total_seats' => $actualStats['total_seats'],
                'normal_seats' => $actualStats['normal_seats'],
                'vip_seats' => $actualStats['vip_seats'],
                'couple_seats' => $actualStats['couple_seats'],
                'disabled_seats' => $actualStats['disabled_seats'],
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', "Đã đồng bộ lại cấu hình ghế cho phòng '{$screen->screen_name}'");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error refreshing seat configuration: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi đồng bộ cấu hình ghế');
        }
    }
}
