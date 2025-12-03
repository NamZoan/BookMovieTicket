<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VnPayController extends Controller
{
    /**
     * Chuyển hướng người dùng đến cổng thanh toán VNPAY.
     * @param Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPayment(Booking $booking)
    {
        $vnp_TmnCode = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = route('booking.vnpay.return'); // Sử dụng route() helper

        $vnp_TxnRef = $booking->booking_code; // Mã đơn hàng
        $vnp_OrderInfo = "Thanh toan don hang {$booking->booking_code}";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $booking->final_amount * 100; // Số tiền thanh toán
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );


        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    /**
     * Xử lý kết quả VNPAY trả về.
     */
    // app/Http/Controllers/VnPayController.php

/**
 * Xử lý kết quả VNPAY trả về.
 */
public function handleReturn(Request $request)
{
    $vnp_HashSecret = env('VNP_HASHSECRET');
    $inputData = $request->all();
    $vnp_SecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash']);

    ksort($inputData);
    $hashData = "";
    foreach ($inputData as $key => $value) {
        $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
    }
    $hashData = ltrim($hashData, '&');

    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    if ($secureHash == $vnp_SecureHash) {
        $bookingCode = $inputData['vnp_TxnRef'];
        $booking = Booking::where('booking_code', $bookingCode)->first();

        if ($booking) {
            // KIỂM TRA SỰ TỒN TẠI CỦA vnp_ResponseCode
            if (isset($inputData['vnp_ResponseCode'])) {
                if ($inputData['vnp_ResponseCode'] == '00') {
                    // Chỉ cập nhật nếu trạng thái đang là Pending
                    if ($booking->payment_status == 'Pending') {
                        DB::beginTransaction();
                        try {
                            $booking->update([
                                'payment_status' => 'Paid',
                                'booking_status' => 'Confirmed',
                                'payment_date' => now()
                            ]);

                            // available_seats đã được giảm khi tạo booking, không cần giảm lại

                            $loyaltyPoints = floor($booking->final_amount / 1000);
                            if ($loyaltyPoints > 0 && $booking->user) {
                                $booking->user->increment('loyalty_points', $loyaltyPoints);
                            }

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('VNPay Return Error (DB Update): ' . $e->getMessage());
                        }
                    }
                    return redirect()->route('booking.confirmation', $booking->booking_id)
                        ->with('success', 'Thanh toán thành công!');
                } else {
                    // Thanh toán thất bại
                    $booking->update([
                        'payment_status' => 'Failed',
                        'booking_status' => 'Cancelled'
                    ]);
                    return redirect()->route('booking.seatSelection', $booking->showtime_id)
                        ->with('error', 'Thanh toán không thành công. Vui lòng thử lại.');
                }
            } else {
                // XỬ LÝ KHI NGƯỜI DÙNG HỦY GIAO DỊCH
                $booking->update([
                    'payment_status' => 'Cancelled',
                    'booking_status' => 'Cancelled'
                ]);
                return redirect()->route('booking.seatSelection', $booking->showtime_id)
                    ->with('error', 'Giao dịch đã bị hủy. Vui lòng thực hiện lại nếu bạn muốn đặt vé.');
            }
        } else {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn đặt vé.');
        }
    } else {
        Log::warning('VNPay Return: Invalid Signature', $inputData);
        return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ. Giao dịch có thể đã bị giả mạo.');
    }
}
}
