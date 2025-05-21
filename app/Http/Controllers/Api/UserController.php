<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $users = $query->latest('user_id')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Pengguna',
            'data' => $users
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:50',
            'email'    => 'required|string|email|max:50|unique:users,email',
            'phone'    => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'address'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dibuat', 'data' => $user], 201);
    }

    public function show(User $user)
    {
        return response()->json(['success' => true, 'message' => 'Detail Pengguna', 'data' => $user], 200);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:50',
            'email'    => 'sometimes|required|string|email|max:50|unique:users,email,' . $user->user_id . ',user_id',
            'phone'    => 'sometimes|required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
            'address'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $updateData = $request->except('password');
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'Pengguna berhasil diperbarui', 'data' => $user->fresh()], 200);
    }

    public function destroy(User $user)
    {
        // Pertimbangkan apa yang terjadi pada order user ini.
        // Sesuai migrasi, order.user_id akan menjadi NULL jika user dihapus.
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus'], 200);
    }
}