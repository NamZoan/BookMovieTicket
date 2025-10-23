<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Hiển thị danh sách người dùng (tìm kiếm + phân trang)
    public function index(Request $request)
    {
        $q = $request->input('q');

        $users = User::query()
            ->when($q, fn($qry) => $qry->where(function($s) use ($q) {
                $s->where('full_name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    // Form tạo mới
    public function create()
    {
        $types = ['Customer','Staff','Admin'];
        return view('admin.users.create', compact('types'));
    }

    // Lưu user mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6|confirmed',
            'user_type' => ['required', Rule::in(['Customer','Staff','Admin'])],
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // nếu không nhập password, tạo password ngẫu nhiên an toàn (admin có thể gửi lại)
            $data['password'] = Hash::make(bin2hex(random_bytes(6)));
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['loyalty_points'] = 0;

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công.');
    }

    // Hiển thị chi tiết user (tùy chọn)
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // Form edit
    public function edit(User $user)
    {
        $types = ['Customer','Staff','Admin'];
        return view('admin.users.edit', compact('user', 'types'));
    }

    // Cập nhật user
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'user_type' => ['required', Rule::in(['Customer','Staff','Admin'])],
            'is_active' => 'nullable|boolean',
            'loyalty_points' => 'nullable|integer|min:0',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->full_name = $data['full_name'];
        $user->user_type = $data['user_type'];
        $user->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : $user->is_active;
        if (isset($data['loyalty_points'])) {
            $user->loyalty_points = $data['loyalty_points'];
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    // "Vô hiệu hóa" tài khoản (set is_active = false)
    public function destroy(User $user)
    {
        $user->is_active = false;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã bị vô hiệu hóa.');
    }

    // Toggle active (Ajax)
    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json(['ok' => true, 'is_active' => $user->is_active]);
    }
}