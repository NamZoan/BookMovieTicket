<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pricing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PricingController extends Controller
{
    // Hiển thị danh sách pricing
    public function index()
    {
        $pricings = Pricing::all();
        return view('admin.pricing.index', compact('pricings'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('admin.pricing.create');
    }

    // Lưu pricing mới
    public function store(Request $request)
    {
        $request->validate([
            'seat_type' => 'required|in:Normal,VIP,Couple',
            'day_type' => 'required|in:Weekday,Weekend,Holiday',
            'time_slot' => 'required|in:Morning,Afternoon,Evening,Late Night',
            'price_multiplier' => 'required|numeric|min:0.01',
        ]);

        Pricing::create($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing created successfully.');
    }

    // Hiển thị chi tiết pricing
    public function show($id)
    {
        $pricing = Pricing::findOrFail($id);
        return view('admin.pricing.show', compact('pricing'));
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $pricing = Pricing::findOrFail($id);
        return view('admin.pricing.edit', compact('pricing'));
    }

    // Cập nhật pricing
    public function update(Request $request, $id)
    {
        $pricing = Pricing::findOrFail($id);

        $request->validate([
            'seat_type' => 'required|in:Normal,VIP,Couple',
            'day_type' => 'required|in:Weekday,Weekend,Holiday',
            'time_slot' => 'required|in:Morning,Afternoon,Evening,Late Night',
            'price_multiplier' => 'required|numeric|min:0.01',
        ]);

        $pricing->update($request->all());

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing updated successfully.');
    }

    // Xóa pricing
    public function destroy($id)
    {
        $pricing = Pricing::findOrFail($id);
        $pricing->delete();

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing deleted successfully.');
    }
}
