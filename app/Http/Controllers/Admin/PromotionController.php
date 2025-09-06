<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index(Request $request): View
    {

        $promotions = Promotion::all();

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create(): View
    {
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created promotion.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:promotions,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:Percentage,Fixed Amount',
            'discount_value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Promotion::create($validator->validated());

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Tạo khuyến mãi thành công.');
    }

    /**
     * Display the specified promotion.
     */
    public function show(string $id): View
    {
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(string $id): View
    {
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified promotion.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:promotions,code,' . $id . ',promotion_id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:Percentage,Fixed Amount',
            'discount_value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $promotion->update($validator->validated());

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Cập nhật khuyến mãi thành công.');
    }

    /**
     * Remove the specified promotion.
     */
    public function destroy(string $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);

        // Kiểm tra nếu khuyến mãi đã được sử dụng
        if ($promotion->used_count > 0) {
            return redirect()->route('admin.promotions.index')
                ->with('error', 'Không thể xóa khuyến mãi đã được sử dụng.');
        }

        $promotion->delete();

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Xóa khuyến mãi thành công.');
    }

    /**
     * Kiểm tra mã khuyến mãi (Ajax request)
     */
    public function checkPromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:promotions,code',
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi không hợp lệ.'
            ], 422);
        }

        $promotion = Promotion::where('code', $request->code)->first();

        if (!$promotion->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi không còn hiệu lực.'
            ], 422);
        }

        if ($request->amount < $promotion->min_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền không đủ để áp dụng khuyến mãi. Tối thiểu: ' . number_format($promotion->min_amount) . ' VNĐ'
            ], 422);
        }

        $discount = $promotion->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'discount_amount' => $discount,
            'final_amount' => $request->amount - $discount,
            'promotion' => $promotion->only(['code', 'name', 'discount_type', 'discount_value'])
        ]);
    }

    /**
     * Kích hoạt/vô hiệu hóa khuyến mãi
     */
    public function toggleStatus(string $id): RedirectResponse
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => !$promotion->is_active]);

        $status = $promotion->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->route('admin.promotions.index')
            ->with('success', "{$status} khuyến mãi thành công.");
    }

    /**
     * Lấy danh sách khuyến mãi còn hiệu lực
     */
    public function activePromotions(): View
    {
        $promotions = Promotion::active()->get();
        return view('admin.promotions.active', compact('promotions'));
    }
}
