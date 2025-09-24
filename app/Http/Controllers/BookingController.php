<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\BookingFood;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\FoodItem;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
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
            if (!$this->isShowtimeAvailable($showtime)) {
                return redirect()->route('movies.showtimes', $showtime->movie_id)
                    ->with('error', 'Suất chiếu này không còn khả dụng.');
            }

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
            $pricing = $this->getPricingInfo($showtime);

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
            'selected_seats' => 'required|string', // JSON string from frontend
            'food_items' => 'nullable|string', // JSON string from frontend
            'payment_method' => 'required|in:Cash,Credit Card,Banking,E-Wallet,Loyalty Points'
        ]);

        // Parse JSON data
        $selectedSeats = json_decode($request->selected_seats, true);
        $foodItems = json_decode($request->food_items, true) ?? [];

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

            // Verify seats are available
            $unavailableSeats = $this->checkSeatAvailability($request->showtime_id, $selectedSeats);
            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Một số ghế đã được đặt bởi người khác. Vui lòng chọn ghế khác.',
                    'unavailable_seats' => $unavailableSeats
                ], 422);
            }

            // Hold seats temporarily (15 minutes)
            $this->holdSeats($request->showtime_id, $selectedSeats, Auth::id());

            // Calculate pricing
            $seatPrices = $this->calculateSeatPrices($seats, $showtime);
            $foodTotal = 0;
            $foodItemsData = [];

            if (!empty($foodItems)) {
                $foodCalculation = $this->calculateFoodPrices($foodItems);
                $foodTotal = $foodCalculation['total'];
                $foodItemsData = $foodCalculation['items'];
            }

            $totalAmount = $seatPrices['total'] + $foodTotal;

            // Store selection in session
            Session::put('booking_data', [
                'showtime_id' => $request->showtime_id,
                'selected_seats' => $selectedSeats,
                'seat_prices' => $seatPrices,
                'food_items' => $foodItemsData,
                'food_total' => $foodTotal,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'expires_at' => Carbon::now()->addMinutes(15)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'seat_count' => count($selectedSeats),
                    'seat_total' => $seatPrices['total'],
                    'food_total' => $foodTotal,
                    'total_amount' => $totalAmount,
                    'payment_url' => route('booking.payment', ['showtime' => $request->showtime_id])
                ]
            ]);

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
    public function processPayment(Request $request)
    {
        $request->validate([
            'promotion_code' => 'nullable|string|max:20',
            'user_notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Get booking data from session
            $bookingData = Session::get('booking_data');

            if (!$bookingData || Carbon::now()->gt($bookingData['expires_at'])) {
                return redirect()->back()
                    ->with('error', 'Thời gian giữ ghế đã hết. Vui lòng đặt lại.');
            }

            $showtime = Showtime::findOrFail($bookingData['showtime_id']);

            // Verify seats are still available
            $unavailableSeats = $this->checkSeatAvailability($bookingData['showtime_id'], $bookingData['selected_seats']);
            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Một số ghế đã được đặt. Vui lòng chọn ghế khác.');
            }

            // Apply promotion if provided
            $discountAmount = 0;
            $promotion = null;

            if ($request->promotion_code) {
                $promotion = $this->validateAndApplyPromotion($request->promotion_code, $bookingData['total_amount']);
                if ($promotion) {
                    $discountAmount = $this->calculateDiscount($promotion, $bookingData['total_amount']);
                }
            }

            $finalAmount = $bookingData['total_amount'] - $discountAmount;

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $bookingData['showtime_id'],
                'booking_code' => $this->generateBookingCode(),
                'total_amount' => $bookingData['total_amount'],
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $bookingData['payment_method'],
                'payment_status' => $this->getInitialPaymentStatus($bookingData['payment_method']),
                'booking_status' => 'Confirmed',
                'booking_date' => Carbon::now(),
                'notes' => $request->user_notes
            ]);

            // Create booking seats
            foreach ($bookingData['selected_seats'] as $seatId) {
                $seatPrice = $bookingData['seat_prices']['seats'][$seatId] ?? $showtime->base_price;

                BookingSeat::create([
                    'booking_id' => $booking->booking_id,
                    'seat_id' => $seatId,
                    'seat_price' => $seatPrice
                ]);
            }

            // Create booking food items
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

            // Update showtime available seats
            $showtime->decrement('available_seats', count($bookingData['selected_seats']));

            // Update promotion usage if applied
            if ($promotion) {
                $promotion->increment('used_count');
            }

            // Process payment based on method
            $paymentResult = $this->processPaymentMethod($booking, $request->payment_method);

            if ($paymentResult['success']) {
                $booking->update([
                    'payment_status' => 'Paid',
                    'payment_date' => Carbon::now()
                ]);

                // Award loyalty points (1 point per 1000 VND spent)
                $loyaltyPoints = floor($finalAmount / 1000);
                if ($loyaltyPoints > 0) {
                    Auth::user()->increment('loyalty_points', $loyaltyPoints);
                }
            }

            // Clear session data
            Session::forget('booking_data');
            $this->releaseHeldSeats($bookingData['showtime_id'], $bookingData['selected_seats']);

            DB::commit();

            return redirect()->route('booking.confirmation', $booking->booking_id)
                ->with('success', 'Đặt vé thành công! Mã đặt vé của bạn là: ' . $booking->booking_code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BookingController@processPayment error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng thử lại.');
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

            return redirect()->route('account.bookings')
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
     * Get pricing information for a showtime (AJAX)
     */
    public function getPricing(Showtime $showtime)
    {
        try {
            $pricing = $this->getPricingInfo($showtime);

            return response()->json([
                'success' => true,
                'data' => $pricing
            ]);

        } catch (\Exception $e) {
            Log::error('BookingController@getPricing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin giá vé.'
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function isShowtimeAvailable(Showtime $showtime)
    {
        $showtimeDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $showtime->show_date . ' ' . $showtime->show_time);

        return $showtime->status === 'Active' &&
               $showtime->available_seats > 0 &&
               $showtimeDateTime->gt(Carbon::now()->addMinutes(10));
    }

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

        return $validHeldSeats->pluck('seat_id')->toArray();
    }

    private function holdSeats($showtime_id, $seat_ids, $user_id, $duration_minutes = 15)
    {
        $cacheKey = "held_seats_{$showtime_id}";
        $heldSeats = cache()->get($cacheKey, []);

        $expiresAt = Carbon::now()->addMinutes($duration_minutes);

        foreach ($seat_ids as $seat_id) {
            $heldSeats[$seat_id] = [
                'seat_id' => $seat_id,
                'user_id' => $user_id,
                'expires_at' => $expiresAt
            ];
        }

        cache()->put($cacheKey, $heldSeats, $expiresAt->addMinutes(5));
        return true;
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

        cache()->put($cacheKey, $heldSeats, Carbon::now()->addHours(1));
    }

    private function checkSeatAvailability($showtime_id, $seat_ids)
    {
        $bookedSeats = $this->getBookedSeats($showtime_id);
        $heldSeats = $this->getHeldSeats($showtime_id, Auth::id());

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

    private function getPricingInfo(Showtime $showtime)
    {
        // Simplified pricing calculation
        // In a real application, you would query the pricing table
        $dayOfWeek = Carbon::createFromFormat('Y-m-d', $showtime->show_date)->dayOfWeek;
        $hour = (int) date('H', strtotime($showtime->show_time));

        $dayType = ($dayOfWeek >= 1 && $dayOfWeek <= 5) ? 'Weekday' : 'Weekend';

        if ($hour >= 6 && $hour < 12) {
            $timeSlot = 'Morning';
            $multiplier = 0.8;
        } elseif ($hour >= 12 && $hour < 18) {
            $timeSlot = 'Afternoon';
            $multiplier = 1.0;
        } elseif ($hour >= 18 && $hour < 22) {
            $timeSlot = 'Evening';
            $multiplier = 1.2;
        } else {
            $timeSlot = 'Late Night';
            $multiplier = 1.5;
        }

        $basePrices = [
            'Normal' => $showtime->base_price * $multiplier,
            'VIP' => $showtime->base_price * $multiplier * 1.5,
            'Couple' => $showtime->base_price * $multiplier * 2,
            'Disabled' => $showtime->base_price * $multiplier * 0.8,
        ];

        return [
            'base_price' => $showtime->base_price,
            'day_type' => $dayType,
            'time_slot' => $timeSlot,
            'prices' => $basePrices
        ];
    }

    private function calculateSeatPrices($seats, $showtime)
    {
        $pricing = $this->getPricingInfo($showtime);
        $seatPrices = [];
        $total = 0;

        foreach ($seats as $seat) {
            $price = $pricing['prices'][$seat->seat_type] ?? $showtime->base_price;
            $seatPrices[$seat->seat_id] = $price;
            $total += $price;
        }

        return [
            'seats' => $seatPrices,
            'total' => $total
        ];
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

    private function processPaymentMethod($booking, $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'Cash':
                return [
                    'success' => true,
                    'message' => 'Vui lòng thanh toán tại rạp khi đến xem phim.'
                ];

            case 'Credit Card':
            case 'Banking':
            case 'E-Wallet':
                // Simulate successful payment for demo
                return [
                    'success' => true,
                    'transaction_id' => 'TXN_' . time() . '_' . $booking->booking_id,
                    'message' => 'Thanh toán thành công.'
                ];

            case 'Loyalty Points':
                return $this->processLoyaltyPointsPayment($booking);

            default:
                return [
                    'success' => false,
                    'message' => 'Phương thức thanh toán không được hỗ trợ.'
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

        $user->decrement('loyalty_points', $requiredPoints);

        return [
            'success' => true,
            'message' => 'Đã thanh toán bằng điểm tích lũy.',
            'points_used' => $requiredPoints
        ];
    }

    private function canCancelBooking(Booking $booking)
    {
        $showtimeDateTime = Carbon::createFromFormat('Y-m-d H:i:s',
            $booking->showtime->show_date . ' ' . $booking->showtime->show_time);

        // Can cancel if more than 30 minutes before showtime
        return Carbon::now()->diffInMinutes($showtimeDateTime, false) > 30;
    }
}
