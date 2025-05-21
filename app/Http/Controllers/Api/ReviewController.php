<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Jika review hanya oleh user yang login

class ReviewController extends Controller
{
    // Index bisa untuk semua review (admin) atau review per order
    public function index(Request $request)
    {
        $query = Review::with(['order.user', 'order.orderItems.menu']); // Eager load relasi terkait

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }
        // Bisa juga filter by user_id dari order
        if ($request->has('user_id')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }


        $reviews = $query->latest('created_at')->paginate(10); // Order by created_at
        return response()->json(['success' => true, 'message' => 'Daftar Ulasan', 'data' => $reviews], 200);
    }

    // User membuat review untuk ordernya
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id',
            'rating'   => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:1000', // Batasi panjang komentar
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);

        // // Validasi: Hanya user yang memesan yang boleh memberi review (jika ada otentikasi)
        // if (Auth::check() && $order->user_id !== Auth::id()) {
        //     return response()->json(['success' => false, 'message' => 'Anda tidak berhak memberi ulasan untuk pesanan ini.'], 403); // Forbidden
        // }

        // Validasi: Order harus sudah 'completed'
        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Pesanan belum selesai, tidak dapat memberi ulasan.'], 400);
        }

        // Validasi: Satu order hanya boleh satu review
        if ($order->review()->exists()) {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah pernah diberi ulasan.'], 400);
        }

        // 'created_at' akan otomatis diisi oleh database (useCurrent()) atau Eloquent (jika $timestamps = true di model)
        $review = Review::create([
            'order_id' => $order->order_id,
            'rating'   => $request->rating,
            'comment'  => $request->comment,
            // 'user_id' bisa ditambahkan di tabel review jika ingin denormalisasi, atau ambil dari $order->user_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dikirim',
            'data'    => $review->load(['order.user'])
        ], 201);
    }

    public function show(Review $review)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Ulasan',
            'data'    => $review->load(['order.user', 'order.orderItems.menu'])
        ], 200);
    }

    // Update review (mungkin oleh user yang membuatnya atau admin)
    public function update(Request $request, Review $review)
    {
        // // Validasi: Hanya user yang membuat review yang boleh mengedit (jika ada otentikasi)
        // // Dan review milik user yang login: $review->order->user_id === Auth::id()
        // if (Auth::check() && $review->order->user_id !== Auth::id()) {
        //     return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengubah ulasan ini.'], 403);
        // }

        $validator = Validator::make($request->all(), [
            'rating'  => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        // 'updated_at' akan otomatis diisi oleh Eloquent jika $timestamps=true di model Review.
        // Jika $timestamps=false, dan ada kolom updated_at, Anda perlu mengisinya manual: $data['updated_at'] = now();
        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'data'    => $review->fresh()->load(['order.user'])
        ], 200);
    }

    public function destroy(Review $review)
    {
        // // Validasi: Hanya user yang membuat review atau admin yang boleh menghapus
        // if (Auth::check() && $review->order->user_id !== Auth::id() && !Auth::user()->isAdmin()) { // Asumsi ada method isAdmin()
        //     return response()->json(['success' => false, 'message' => 'Anda tidak berhak menghapus ulasan ini.'], 403);
        // }

        $review->delete();
        return response()->json(['success' => true, 'message' => 'Ulasan berhasil dihapus'], 200);
    }
}