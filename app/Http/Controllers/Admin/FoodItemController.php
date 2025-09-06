<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $status = $request->input('status');
        
        $items = FoodItem::all();

        $categories = FoodItem::CATEGORIES;
        
        return view('admin.food_items.index', compact('items', 'category', 'status', 'categories'));
    }

    public function create()
    {
        $categories = FoodItem::CATEGORIES;
        return view('admin.food_items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:food_items,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'category'    => ['required', 'in:' . implode(',', FoodItem::CATEGORIES)],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'image_url'   => ['nullable', 'url', 'max:500'],
            'is_available'=> ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->boolean('is_available');
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'food_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/food_items', $filename);
            $data['image_url'] = Storage::url($path);
        }

        $item = FoodItem::create($data);

        return redirect()
            ->route('admin.food-items.show', $item->item_id)
            ->with('success', 'Tạo món ăn thành công!');
    }

    public function show(FoodItem $food_item)
    {
        return view('admin.food_items.show', ['item' => $food_item]);
    }

    public function edit(FoodItem $food_item)
    {
        $categories = FoodItem::CATEGORIES;
        return view('admin.food_items.edit', ['item' => $food_item, 'categories' => $categories]);
    }

    public function update(Request $request, FoodItem $food_item)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:food_items,name,' . $food_item->item_id . ',item_id'],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'category'    => ['required', 'in:' . implode(',', FoodItem::CATEGORIES)],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'image_url'   => ['nullable', 'url', 'max:500'],
            'is_available'=> ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->boolean('is_available');
        $data['updated_at'] = now();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($food_item->image_url && !Str::startsWith($food_item->image_url, 'http')) {
                $oldPath = str_replace('/storage/', 'public/', $food_item->image_url);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }
            
            $image = $request->file('image');
            $filename = 'food_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/food_items', $filename);
            $data['image_url'] = Storage::url($path);
        }

        $food_item->update($data);

        return redirect()
            ->route('admin.food-items.show', $food_item->item_id)
            ->with('success', 'Cập nhật món ăn thành công!');
    }

    public function destroy(FoodItem $food_item)
    {
        // Delete image file if exists
        if ($food_item->image_url && !Str::startsWith($food_item->image_url, 'http')) {
            $path = str_replace('/storage/', 'public/', $food_item->image_url);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $food_item->delete();

        return redirect()
            ->route('admin.food-items.index')
            ->with('success', 'Đã xóa món ăn thành công!');
    }

    public function toggleStatus(FoodItem $food_item)
    {
        $food_item->update([
            'is_available' => !$food_item->is_available,
            'updated_at' => now()
        ]);

        $status = $food_item->is_available ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()
            ->route('admin.food-items.index')
            ->with('success', "Đã {$status} món ăn '{$food_item->name}'!");
    }
}
