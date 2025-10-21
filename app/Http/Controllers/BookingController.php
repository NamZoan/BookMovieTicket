<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\BookingFood;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\FoodItem;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\SeatSelected;
use App\Events\SeatReleased;

class BookingController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }
    /**
     * Display seat selection page
     */
    public function seatSelection(Showtime $showtime)
    {
        try {
            // Get showtime with related data
            $showtime->load([
                'movie',
                'screen.cinema',
                'screen.seats' => function ($query) {
                    $query->orderBy('row_name')->orderBy('seat_number');
                }
            ]);


            // Check if showtime is available
            // if (!$this->isShowtimeAvailable($showtime)) {
            //     return redirect()->route('movies.showtimes', $showtime->movie_id)
            //         ->with('error', 'Suất chiếu này không còn khả dụng.');
            // }
            // Get booked seats for this showtime
            $bookedSeats = $this->getBookedSeats($showtime->showtime_id);

            // Get held seats (temporarily reserved by other users)
            $heldSeats = $this->getHeldSeats($showtime->showtime_id);

            // Get available food items
            $foodItems = FoodItem::where('is_available', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category');
            // Get pricing information


            $pricing = [
                'Normal' => $showtime->price_seat_normal,
                'VIP' => $showtime->price_seat_vip,
                'Couple' => $showtime->price_seat_couple
            ];

            // Generate seat map
            $seatMap = $this->generateSeatMap($showtime->screen->seats, $bookedSeats, $heldSeats);
            return view('client.booking.seat-selection', compact(
                'showtime',
                'seatMap',
                'bookedSeats',
                'heldSeats',
                'foodItems',
                'pricing'
            ));
        } catch (\Exception $e) {
            Log::error('BookingController@seatSelection error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Không thể tải trang chọn ghế. Vui lòng thử lại.');
        }
    }



    /**
     * Handle seat selection (AJAX)
     */
    public function selectSeats(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,showtime_id',
            'selected_seats' => 'required|string',
        ]);

        // Kiểm tra xem selected_seats có phải là chuỗi JSON không
        if (is_string($request->selected_seats)) {
            $selectedSeats = json_decode($request->selected_seats, true);
        } else {
            // Nếu đã là mảng, gán trực tiếp
            $selectedSeats = $request->selected_seats;
        }

        // Kiểm tra và chuẩn hoá dữ liệu food_items từ request
        $foodItems = [];
        if ($request->has('food_items')) {
            if (is_string($request->food_items)) {
                $foodItems = json_decode($request->food_items, true) ?? [];
            } elseif (is_array($request->food_items)) {
                $foodItems = $request->food_items;
            }
        }

        // Validate parsed data
        if (!is_array($selectedSeats) || empty($selectedSeats)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một ghế.'
            ], 422);
        }

        if (count($selectedSeats) > 8) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chọn quá 8 ghế.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $showtime = Showtime::findOrFail($request->showtime_id);
            $seats = Seat::whereIn('seat_id', $selectedSeats)->get();

            // Verify seats are available (exclude current user's temporary holds)
            $unavailableSeats = $this->checkSeatAvailability($request->showtime_id, $selectedSeats, Auth::id());
            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Một số ghế đã được đặt bởi người khác. Vui lòng chọn ghế khác.',
                    'unavailable_seats' => $unavailableSeats
                ], 422);
            }

            // Hold seats temporarily (3 minutes)
            $this->holdSeats($request->showtime_id, $selectedSeats, Auth::id());

            // Calculate pricing
            // Calculate seat prices (per seat and total)
            $seatPrices = [
                'seats' => [],
                'total' => 0
            ];

            foreach ($seats as $seat) {
                // Determine price based on seat type
                switch (strtolower($seat->seat_type)) {
                    case 'vip':
                        $price = $showtime->price_seat_vip;
                        break;
                    case 'couple':
                        $price = $showtime->price_seat_couple;
                        break;
                    default:
                        $price = $showtime->price_seat_normal;
                        break;
                }

                $seatPrices['seats'][$seat->seat_id] = $price;
                $seatPrices['total'] += $price;
            }

            // Normalize food items into expected structure: array of ['item_id'=>..., 'quantity'=>...]
            $normalizedFoodItems = [];
            if (!empty($foodItems) && is_array($foodItems)) {
                // If structure is id => qty (typical from form inputs named food_items[<id>])
                $values = array_values($foodItems);
                $first = reset($values);

                if (!is_array($first)) {
                    foreach ($foodItems as $k => $v) {
                        // Only include positive quantities
                        $qty = is_numeric($v) ? (int) $v : 0;
                        if ($qty > 0) {
                            $normalizedFoodItems[] = [
                                'item_id' => (int) $k,
                                'quantity' => $qty
                            ];
                        }
                    }
                } else {
                    // Already in array-of-arrays shape
                    foreach ($foodItems as $it) {
                        if (is_array($it) && isset($it['item_id'])) {
                            $qty = isset($it['quantity']) ? (int) $it['quantity'] : 0;
                            if ($qty > 0) {
                                $normalizedFoodItems[] = [
                                    'item_id' => (int) $it['item_id'],
                                    'quantity' => $qty
                                ];
                            }
                        }
                    }
                }
            }

            // Calculate food totals using normalized items
            $foodTotal = 0;
            $foodItemsData = [];
            if (!empty($normalizedFoodItems)) {
                $foodCalculation = $this->calculateFoodPrices($normalizedFoodItems);
                $foodTotal = $foodCalculation['total'];
                $foodItemsData = $foodCalculation['items'];
            }

            $totalAmount = $seatPrices['total'] + $foodTotal;

            // Xử lý promotion nếu có
            $promotionCode = $request->promotion_code;
            $discountAmount = 0;
            $finalAmount = $totalAmount;
            $promotionData = null;

            if ($promotionCode) {
                $promotionResult = $this->promotionService->validateAndApplyPromotion(
                    $promotionCode,
                    $totalAmount,
                    Auth::id()
                );

                if ($promotionResult['success']) {
                    $discountAmount = $promotionResult['discount_amount'];
                    $finalAmount = $totalAmount - $discountAmount;
                    $promotionData = $promotionResult['promotion'];
                }
            }

            // Store selection in session
            Session::put('booking_data', [
                'showtime_id' => $request->showtime_id,
                'selected_seats' => $selectedSeats,
                'seat_prices' => $seatPrices,
                'food_items' => $foodItemsData,
                'food_total' => $foodTotal,
                'total_amount' => $totalAmount,
                'promotion_code' => $promotionCode,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'promotion_data' => $promotionData,
                'payment_method' => $request->payment_method,
                'expires_at' => Carbon::now()->addMinutes(3)
            ]);

            DB::commit();

            // If the request expects JSON (AJAX), return JSON response
            $accept = $request->header('Accept', '');
            $acceptHasJson = (stripos($accept, 'application/json') !== false) || (stripos($accept, '/json') !== false) || (stripos($accept, '+json') !== false);

            if ($request->wantsJson() || $request->ajax() || $acceptHasJson) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'seat_count' => count($selectedSeats),
                        'seat_total' => $seatPrices['total'],
                        'food_total' => $foodTotal,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'promotion_code' => $promotionCode,
                        'payment_url' => route('booking.payment', ['showtime' => $request->showtime_id])
                    ]
                ]);
            }

            // For normal form submission (non-AJAX), redirect user to payment page
            return redirect()->route('booking.payment', ['showtime' => $request->showtime_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BookingController@selectSeats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chọn ghế. Vui lòng thử lại.'
            ], 500);
        }
    }



    /**
     * Display payment page
     */
    public function payment(Showtime $showtime)
    {
        try {
            // Get booking data from session
            $bookingData = Session::get('booking_data');

            if (!$bookingData || Carbon::now()->gt($bookingData['expires_at'])) {
                return redirect()->route('booking.seatSelection', $showtime)
                    ->with('error', 'Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');
            }

            $showtime->load(['movie', 'screen.cinema']);
            $selectedSeats = Seat::whereIn('seat_id', $bookingData['selected_seats'])->get();

            // Get available promotions
            $promotions = Promotion::where('is_active', true)
                ->where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today())
                ->where('usage_limit', '>', DB::raw('used_count'))
                ->get();

            return view('client.booking.payment', compact(
                'showtime',
                'selectedSeats',
                'bookingData',
                'promotions'
            ));
        } catch (\Exception $e) {
            Log::error('BookingController@payment error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Không thể tải trang thanh toán. Vui lòng thử lại.');
        }
    }

    /**
     * Process payment and create booking
     */
    // app/Http/Controllers/BookingController.php

    public function processPayment(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:191',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:100',
            'payment_method' => 'required|string|in:VNPAY,Cash',
            'user_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $bookingData = Session::get('booking_data');

            if (!$bookingData || Carbon::now()->gt($bookingData['expires_at'])) {
                return redirect()->route('booking.seatSelection', $bookingData['showtime_id'] ?? 0)
                    ->with('error', 'Phiên đặt vé đã hết hạn. Vui lòng thử lại.');
            }

            $showtime = Showtime::findOrFail($bookingData['showtime_id']);

            $unavailableSeats = $this->checkSeatAvailability($bookingData['showtime_id'], $bookingData['selected_seats'], Auth::id());
            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Một số ghế đã được đặt. Vui lòng chọn ghế khác.');
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'showtime_id' => $bookingData['showtime_id'],
                'booking_code' => $this->generateBookingCode(),
                'total_amount' => $bookingData['total_amount'],
                'promotion_code' => $bookingData['promotion_code'],
                'discount_amount' => $bookingData['discount_amount'],
                'final_amount' => $bookingData['final_amount'],
                'payment_method' => $request->payment_method,
                'payment_status' => 'Pending',
                'booking_status' => 'Pending',
                'booking_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(15), // Thời gian chờ thanh toán
                'notes' => $request->user_notes
            ]);

            foreach ($bookingData['selected_seats'] as $seatId) {
                BookingSeat::create([
                    'booking_id' => $booking->booking_id,
                    'seat_id' => $seatId,
                    'seat_price' => $bookingData['seat_prices']['seats'][$seatId] ?? 0
                ]);
            }

            if (!empty($bookingData['food_items'])) {
                foreach ($bookingData['food_items'] as $foodItem) {
                    BookingFood::create([
                        'booking_id' => $booking->booking_id,
                        'item_id' => $foodItem['item_id'],
                        'quantity' => $foodItem['quantity'],
                        'unit_price' => $foodItem['unit_price'],
                        'total_price' => $foodItem['total_price']
                    ]);
                }
            }

            // Xóa ghế đã giữ trong cache
            $this->releaseHeldSeats($bookingData['showtime_id'], $bookingData['selected_seats'], Auth::id());

            // Giảm số ghế có sẵn của suất chiếu ngay khi tạo booking
            $showtime->decrement('available_seats', count($bookingData['selected_seats']));

            DB::commit();

            // Xóa session sau khi đã tạo booking thành công
            Session::forget('booking_data');

            if ($request->payment_method === 'VNPAY') {
                // Chuyển sang VnPayController để xử lý
                return app(VnPayController::class)->createPayment($booking);
            } elseif ($request->payment_method === 'Cash') {
                // Cập nhật trạng thái cho thanh toán tại quầy
                $booking->update([
                    'booking_status' => 'Confirmed',
                    'payment_status' => 'Pending'
                ]);
                return redirect()->route('booking.confirmation', $booking->booking_id)->with('success', 'Đặt vé thành công!');
            }

            return redirect()->route('home')->with('error', 'Phương thức thanh toán không hợp lệ.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BookingController@processPayment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình đặt vé. Vui lòng thử lại.');
        }
    }

    /**
     * Display booking confirmation
     */
    public function confirmation(Booking $booking)
    {
        try {
            // Check if user owns this booking
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền xem đơn đặt vé này.');
            }

            $booking->load([
                'user',
                'showtime.movie',
                'showtime.screen.cinema',
                'bookingSeats.seat',
                'bookingFoods.item'
            ]);

            return view('client.booking.confirmation', compact('booking'));
        } catch (\Exception $e) {
            Log::error('BookingController@confirmation error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Không tìm thấy thông tin đặt vé.');
        }
    }

    /**
     * Display ticket (printable version)
     */
    public function ticket(Booking $booking)
    {
        try {
            // Check if user owns this booking
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền xem vé này.');
            }

            $booking->load([
                'user',
                'showtime.movie',
                'showtime.screen.cinema',
                'bookingSeats.seat',
                'bookingFoods.item'
            ]);

            return view('client.booking.ticket', compact('booking'));
        } catch (\Exception $e) {
            Log::error('BookingController@ticket error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Không tìm thấy thông tin vé.');
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        DB::beginTransaction();

        try {
            // Check if user owns this booking
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền hủy đơn đặt vé này.');
            }

            // Check if booking can be cancelled
            if (!$this->canCancelBooking($booking)) {
                return redirect()->back()
                    ->with('error', 'Không thể hủy vé trong vòng 30 phút trước giờ chiếu.');
            }

            // Update booking status
            $booking->update([
                'booking_status' => 'Cancelled',
                'payment_status' => $booking->payment_status === 'Paid' ? 'Refunded' : 'Failed'
            ]);

            // Return seats to available pool
            $seatCount = $booking->bookingSeats->count();
            $booking->showtime->increment('available_seats', $seatCount);

            // Process refund if payment was made
            if ($booking->payment_status === 'Refunded') {
                // Here you would integrate with payment gateway for refund
                // For now, we'll just log it
                Log::info("Refund processed for booking: {$booking->booking_code}");
            }

            DB::commit();

            return redirect()->route('user.bookings.index')
                ->with('success', 'Đã hủy vé thành công. Tiền sẽ được hoàn lại trong 3-5 ngày làm việc.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BookingController@cancel error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Không thể hủy vé. Vui lòng thử lại hoặc liên hệ hỗ trợ.');
        }
    }

    /**
     * Get available seats for a showtime (AJAX)
     */
    public function getAvailableSeats(Showtime $showtime)
    {
        try {
            $showtime->load('screen.seats');

            $bookedSeats = $this->getBookedSeats($showtime->showtime_id);
            $heldSeats = $this->getHeldSeats($showtime->showtime_id, Auth::id());

            $unavailableSeats = array_merge($bookedSeats, $heldSeats);

            $availableSeats = $showtime->screen->seats->whereNotIn('seat_id', $unavailableSeats);

            return response()->json([
                'success' => true,
                'data' => [
                    'available_seats' => $availableSeats->values(),
                    'booked_seats' => $bookedSeats,
                    'held_seats' => $heldSeats,
                    'total_seats' => $showtime->screen->total_seats,
                    'available_count' => $availableSeats->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('BookingController@getAvailableSeats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin ghế.'
            ], 500);
        }
    }

    /**
     * Hold seats temporarily (AJAX)
     */
    public function holdSeat(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,showtime_id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,seat_id'
        ]);

        try {
            $result = $this->holdSeats($request->showtime_id, $request->seat_ids, Auth::id());

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Ghế đã được giữ tạm thời.' : 'Một số ghế không thể giữ.'
            ]);
        } catch (\Exception $e) {
            Log::error('BookingController@holdSeats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi giữ ghế.'
            ], 500);
        }
    }

    /**
     * Release held seats (AJAX)
     */
    public function releaseSeats(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,showtime_id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,seat_id'
        ]);

        try {
            $this->releaseHeldSeats($request->showtime_id, $request->seat_ids, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Đã bỏ giữ ghế.'
            ]);
        } catch (\Exception $e) {
            Log::error('BookingController@releaseSeats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bỏ giữ ghế.'
            ], 500);
        }
    }

    /**
     * Validate promotion code (AJAX)
     */
    public function validatePromotion(Request $request)
    {
        $request->validate([
            'promotion_code' => 'required|string|max:20',
            'total_amount' => 'required|numeric|min:0'
        ]);

        try {
            $result = $this->promotionService->validateAndApplyPromotion(
                $request->promotion_code,
                $request->total_amount,
                Auth::id()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('BookingController@validatePromotion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra mã giảm giá.'
            ], 500);
        }
    }

    /**
     * Get available promotions (AJAX)
     */
    public function getAvailablePromotions(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0'
        ]);

        try {
            $promotions = $this->promotionService->getAvailablePromotions(
                Auth::id(),
                $request->total_amount
            );

            return response()->json([
                'success' => true,
                'data' => $promotions
            ]);
        } catch (\Exception $e) {
            Log::error('BookingController@getAvailablePromotions error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách mã giảm giá.'
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    // private function isShowtimeAvailable(Showtime $showtime)
    // {
    //     dd($showtime);
    //     $showtimeDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $showtime->show_date . ' ' . $showtime->show_time);

    //     return $showtime->status === 'Active' &&
    //            $showtime->available_seats > 0 &&
    //            $showtimeDateTime->gt(Carbon::now()->addMinutes(10));
    // }

    private function getBookedSeats($showtime_id)
    {
        return BookingSeat::join('bookings', 'booking_seats.booking_id', '=', 'bookings.booking_id')
            ->where('bookings.showtime_id', $showtime_id)
            ->whereIn('bookings.booking_status', ['Confirmed', 'Used'])
            ->pluck('booking_seats.seat_id')
            ->toArray();
    }

    private function getHeldSeats($showtime_id, $excludeUserId = null)
    {
        $cacheKey = "held_seats_{$showtime_id}";
        $heldSeats = cache()->get($cacheKey, []);

        // Filter out expired holds and current user's holds
        $validHeldSeats = collect($heldSeats)->filter(function ($hold) use ($excludeUserId) {
            return Carbon::now()->lt($hold['expires_at']) &&
                ($excludeUserId === null || $hold['user_id'] !== $excludeUserId);
        });

        // Clean up expired holds and update cache
        $expiredSeats = [];
        $validHolds = [];
        foreach ($heldSeats as $seatId => $hold) {
            if (Carbon::now()->lt($hold['expires_at'])) {
                $validHolds[$seatId] = $hold;
            } else {
                $expiredSeats[] = $seatId;
            }
        }

        // If there are expired seats, update cache and broadcast release
        if (!empty($expiredSeats)) {
            cache()->put($cacheKey, $validHolds, Carbon::now()->addHours(1));
            broadcast(new SeatReleased($expiredSeats, $showtime_id))->toOthers();
        }

        return $validHeldSeats->pluck('seat_id')->toArray();
    }

    private function holdSeats($showtime_id, $seat_ids, $user_id, $duration_minutes = 3)
    {
        $cacheKey = "held_seats_{$showtime_id}";
        $heldSeats = cache()->get($cacheKey, []);

        $expiresAt = Carbon::now()->addMinutes($duration_minutes);
        $successfullyHeld = [];

        foreach ($seat_ids as $seat_id) {
            // Check if seat is already held by someone else
            if (isset($heldSeats[$seat_id])) {
                $existingHold = $heldSeats[$seat_id];
                // If the hold is still valid and not by current user, skip this seat
                if (Carbon::now()->lt($existingHold['expires_at']) && $existingHold['user_id'] !== $user_id) {
                    continue;
                }
            }

            $heldSeats[$seat_id] = [
                'seat_id' => $seat_id,
                'user_id' => $user_id,
                'expires_at' => $expiresAt
            ];
            $successfullyHeld[] = $seat_id;
        }

        // Only broadcast if we successfully held some seats
        if (!empty($successfullyHeld)) {
            // Broadcast the seat selection event to other clients
            broadcast(new SeatSelected($successfullyHeld, $showtime_id))->toOthers();
            cache()->put($cacheKey, $heldSeats, $expiresAt->addMinutes(1));
        }

        return !empty($successfullyHeld);
    }

    private function releaseHeldSeats($showtime_id, $seat_ids, $user_id = null)
    {
        $cacheKey = "held_seats_{$showtime_id}";
        $heldSeats = cache()->get($cacheKey, []);

        foreach ($seat_ids as $seat_id) {
            if (isset($heldSeats[$seat_id])) {
                if ($user_id === null || $heldSeats[$seat_id]['user_id'] === $user_id) {
                    unset($heldSeats[$seat_id]);
                }
            }
        }

        // Broadcast the seat release event to other clients
        broadcast(new SeatReleased($seat_ids, $showtime_id))->toOthers();
        cache()->put($cacheKey, $heldSeats, Carbon::now()->addHours(1));
    }

    private function checkSeatAvailability($showtime_id, $seat_ids, $excludeUserId = null)
    {
        $bookedSeats = $this->getBookedSeats($showtime_id);
        $heldSeats = $this->getHeldSeats($showtime_id, $excludeUserId);

        $unavailableSeats = array_merge($bookedSeats, $heldSeats);
        return array_intersect($seat_ids, $unavailableSeats);
    }

    private function generateSeatMap($seats, $bookedSeats, $heldSeats)
    {
        $seatMap = [];

        foreach ($seats as $seat) {
            $status = 'available';

            if (in_array($seat->seat_id, $bookedSeats)) {
                $status = 'booked';
            } elseif (in_array($seat->seat_id, $heldSeats)) {
                $status = 'held';
            }

            $seatMap[$seat->row_name][$seat->seat_number] = [
                'seat_id' => $seat->seat_id,
                'seat_type' => $seat->seat_type,
                'status' => $status,
                'row' => $seat->row_name,
                'number' => $seat->seat_number
            ];
        }

        ksort($seatMap);

        foreach ($seatMap as $row => &$seats) {
            ksort($seats);
        }

        return $seatMap;
    }



    private function calculateFoodPrices($foodItems)
    {
        $items = [];
        $total = 0;

        foreach ($foodItems as $item) {
            $foodItem = FoodItem::findOrFail($item['item_id']);
            $itemTotal = $foodItem->price * $item['quantity'];

            $items[] = [
                'item_id' => $item['item_id'],
                'name' => $foodItem->name,
                'quantity' => $item['quantity'],
                'unit_price' => $foodItem->price,
                'total_price' => $itemTotal
            ];

            $total += $itemTotal;
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    private function generateBookingCode()
    {
        do {
            $code = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    private function validateAndApplyPromotion($code, $totalAmount)
    {
        $promotion = Promotion::where('code', $code)
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where('usage_limit', '>', DB::raw('used_count'))
            ->first();

        if (!$promotion || $totalAmount < $promotion->min_amount) {
            return null;
        }

        return $promotion;
    }

    private function calculateDiscount($promotion, $totalAmount)
    {
        if ($promotion->discount_type === 'Percentage') {
            $discount = ($totalAmount * $promotion->discount_value) / 100;

            if ($promotion->max_discount && $discount > $promotion->max_discount) {
                $discount = $promotion->max_discount;
            }
        } else {
            $discount = $promotion->discount_value;
        }

        return min($discount, $totalAmount);
    }

    private function getInitialPaymentStatus($paymentMethod)
    {
        switch ($paymentMethod) {
            case 'Cash':
                return 'Pending';
            case 'Credit Card':
            case 'Banking':
            case 'E-Wallet':
                return 'Pending';
            case 'Loyalty Points':
                return 'Paid';
            default:
                return 'Pending';
        }
    }

    private function processOnlinePayment($booking)
    {
        // Simulate online payment processing
        // In real implementation, this would integrate with payment gateways like VNPay, MoMo, etc.

        try {
            // Simulate payment gateway response (95% success rate for demo)
            $isSuccessful = rand(1, 100) <= 95;

            if ($isSuccessful) {
                $transactionId = 'TXN_' . time() . '_' . $booking->booking_id;

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'message' => 'Thanh toán thành công.',
                    'gateway_response' => [
                        'status' => 'success',
                        'transaction_id' => $transactionId,
                        'amount' => $booking->final_amount,
                        'currency' => 'VND',
                        'timestamp' => Carbon::now()->toISOString()
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Thanh toán thất bại. Vui lòng thử lại hoặc liên hệ ngân hàng.',
                    'gateway_response' => [
                        'status' => 'failed',
                        'error_code' => 'PAYMENT_FAILED',
                        'error_message' => 'Insufficient funds or card declined'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.',
                'gateway_response' => [
                    'status' => 'error',
                    'error_code' => 'SYSTEM_ERROR',
                    'error_message' => $e->getMessage()
                ]
            ];
        }
    }

    private function processLoyaltyPointsPayment($booking)
    {
        $user = Auth::user();
        $requiredPoints = ceil($booking->final_amount / 1000); // 1 point = 1000 VND

        if ($user->loyalty_points < $requiredPoints) {
            return [
                'success' => false,
                'message' => 'Không đủ điểm tích lũy. Bạn cần ' . $requiredPoints . ' điểm.'
            ];
        }

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->decrement('loyalty_points', $requiredPoints);

        return [
            'success' => true,
            'message' => 'Đã thanh toán bằng điểm tích lũy.',
            'points_used' => $requiredPoints
        ];
    }

    private function canCancelBooking(Booking $booking)
    {
        $showtimeDateTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $booking->showtime->show_date . ' ' . $booking->showtime->show_time
        );

        // Can cancel if more than 30 minutes before showtime
        return Carbon::now()->diffInMinutes($showtimeDateTime, false) > 30;
    }
}
