<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Pastikan model User di-import
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:50',
            'email'    => 'required|string|email|max:50|unique:users,email',
            'phone'    => 'required|string|max:15', // Anda bisa tambahkan validasi format nomor telepon jika perlu
            'password' => 'required|string|min:8|confirmed', // 'confirmed' akan mencari field 'password_confirmation'
            'address'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Periksa input Anda.',
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'address'  => $request->address,
        ]);

        // Anda bisa memilih untuk langsung login setelah registrasi atau tidak.
        // Jika ya, buat token di sini. Untuk sekarang, kita minta user login manual setelah registrasi.
        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Silakan login.',
            'data'    => $user
        ], 201); // Created
    }

    /**
     * Authenticate a user and return a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal. Periksa input Anda.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Coba autentikasi user
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401); // Unauthorized
        }

        // Ambil user yang berhasil login
        $user = User::where('email', $request->email)->firstOrFail();

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user' => $user, // Kirim data user juga
                'token' => $token,
                'token_type' => 'Bearer', // Standar untuk token type
            ]
        ], 200);
    }

    /**
     * Get the authenticated User.
     * Ini bisa menggantikan closure /api/user Anda saat ini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diambil.',
            'data'    => $request->user() // Mengambil user yang terautentikasi via Sanctum
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Menghapus token yang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ], 200);
    }

    /**
     * (Opsional) Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // Auth::user();

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:50',
            // Gunakan user_id saat validasi unique untuk email, agar tidak konflik dengan email user sendiri
            'email'    => 'sometimes|required|string|email|max:50|unique:users,email,' . $user->user_id . ',user_id',
            'phone'    => 'sometimes|required|string|max:15',
            'address'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Update profil gagal', 'errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui', 'data' => $user->fresh()], 200);
    }

    /**
     * (Opsional) Change user password.
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Ganti password gagal', 'errors' => $validator->errors()], 422);
        }

        // Cek apakah password saat ini cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini tidak cocok.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah.'], 200);
    }
}