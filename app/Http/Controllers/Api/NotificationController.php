<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationModel; // Ganti nama model
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    // Index bisa untuk admin melihat semua notif, atau user melihat notifnya sendiri
    public function index(Request $request)
    {
        $query = NotificationModel::with('user'); // Eager load user

        // Jika ada user_id di request (misal, user ambil notifnya sendiri atau admin filter)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Filter berdasarkan status dibaca
        if ($request->has('is_read')) {
            $query->where('is_read', filter_var($request->is_read, FILTER_VALIDATE_BOOLEAN));
        }

        // // Contoh: Jika ini endpoint untuk user yang sedang login mengambil notifnya
        // // Pastikan ada otentikasi
        // if (auth()->check() && !$request->has('user_id')) { // Jika tidak ada user_id spesifik, ambil milik user login
        //     $query->where('user_id', auth()->id());
        // }

        $notifications = $query->latest('created_at')->paginate(15); // Order by created_at
        return response()->json(['success' => true, 'message' => 'Daftar Notifikasi', 'data' => $notifications], 200);
    }

    // Membuat notifikasi (biasanya oleh sistem atau admin)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'message' => 'required|string',
            'is_read' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (!$request->filled('is_read')) {
            $data['is_read'] = false; // Default
        }
        // 'created_at' akan otomatis diisi oleh database (useCurrent()) atau Eloquent (jika $timestamps = true)

        $notification = NotificationModel::create($data);
        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dibuat', 'data' => $notification->load('user')], 201);
    }

    public function show(NotificationModel $notification) // Gunakan NotificationModel
    {
        // Otomatis tandai sebagai dibaca saat dilihat
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
        return response()->json(['success' => true, 'message' => 'Detail Notifikasi', 'data' => $notification->load('user')], 200);
    }

    // Update notifikasi (misal, tandai sebagai dibaca/belum dibaca)
    public function update(Request $request, NotificationModel $notification)
    {
        $validator = Validator::make($request->all(), [
            'is_read' => 'sometimes|required|boolean',
            'message' => 'sometimes|required|string', // Jika admin boleh edit pesan
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $notification->update($request->only(['is_read', 'message']));
        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil diperbarui', 'data' => $notification->fresh()->load('user')], 200);
    }

    // Menandai semua notifikasi user sebagai dibaca
    public function markAllAsRead(Request $request)
    {
        // Asumsi user_id didapat dari user yang login atau dari request
        $userId = $request->input('user_id', auth()->id());
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID tidak ditemukan.'], 400);
        }

        NotificationModel::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai dibaca.'], 200);
    }


    public function destroy(NotificationModel $notification)
    {
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus'], 200);
    }
}