<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Untuk transactions

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.menu', 'payment', 'delivery.driver', 'review']); // Eager load relasi

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->latest('order_id')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Pesanan',
            'data' => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'nullable|exists:users,user_id', // User bisa guest
            'order_type'     => 'required|in:pickup,delivery',
            'payment_method' => 'required|string|in:cash,transfer,e-wallet,QRIS', // Sesuai ENUM di DB jika ada
            'delivery_address' => 'required_if:order_type,delivery|nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,menu_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes'  => 'nullable|string',
            // Tambahkan validasi untuk ongkir jika perlu
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                if (!$menu || !$menu->is_available || $menu->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu ' . ($menu ? $menu->name : 'ID ' . $item['menu_id']) . ' tidak tersedia atau stok tidak cukup.'
                    ], 400); // Bad Request
                }
                $totalPrice += $menu->price * $item['quantity'];
                $orderItemsData[] = [
                    'menu_id'  => $menu->menu_id,
                    'quantity' => $item['quantity'],
                    'notes'    => $item['notes'] ?? null,
                    // 'price_at_order' => $menu->price // Simpan harga saat order jika perlu
                ];

                // Kurangi stok menu
                $menu->decrement('stock', $item['quantity']);
            }

            // Tambahkan logika ongkir jika order_type == 'delivery'
            // $deliveryFee = ($request->order_type == 'delivery') ? 10000 : 0; // Contoh
            // $totalPrice += $deliveryFee;

            $order = Order::create([
                'user_id'         => $request->user_id,
                'order_type'      => $request->order_type,
                'status'          => 'pending', // Status awal
                'total_price'     => $totalPrice,
                'payment_method'  => $request->payment_method,
                'delivery_address'=> $request->order_type == 'delivery' ? $request->delivery_address : null,
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
            }

            // Jika payment method bukan 'cash', mungkin buat record payment dengan status 'pending'
            if ($request->payment_method !== 'cash') {
                $order->payment()->create([
                    'amount' => $totalPrice,
                    'status' => 'pending',
                    // 'payment_time' akan diisi saat pembayaran dikonfirmasi
                ]);
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data'    => $order->load(['user', 'orderItems.menu', 'payment'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Kembalikan stok jika terjadi error setelah stok dikurangi
            if (!empty($request->items)) {
                foreach ($request->items as $itemRequest) {
                    $menu = Menu::find($itemRequest['menu_id']);
                    if ($menu) {
                        $menu->increment('stock', $itemRequest['quantity']);
                    }
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Pesanan',
            'data'    => $order->load(['user', 'orderItems.menu', 'payment', 'delivery.driver', 'review'])
        ], 200);
    }

    public function update(Request $request, Order $order)
    {
        // Update order biasanya terbatas pada status atau detail tertentu,
        // bukan item atau total harga setelah dibuat (kecuali ada pembatalan item)
        $validator = Validator::make($request->all(), [
            'status'         => 'sometimes|required|string|in:pending,paid,processing,ready,completed,canceled,failed_payment',
            'delivery_address' => 'sometimes|required_if:order_type,delivery|nullable|string',
            // Tambahkan field lain yang boleh diupdate
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        // Logika khusus jika status berubah ke 'canceled'
        if ($request->filled('status') && $request->status === 'canceled' && $order->status !== 'canceled') {
            DB::beginTransaction();
            try {
                // Kembalikan stok menu
                foreach ($order->orderItems as $item) {
                    $menu = Menu::find($item->menu_id);
                    if ($menu) {
                        $menu->increment('stock', $item->quantity);
                    }
                }
                $order->update($request->only(['status', 'delivery_address'])); // Update field yang diizinkan

                // Update status payment jika ada
                if ($order->payment && $order->payment->status !== 'success') {
                    $order->payment->update(['status' => 'canceled']);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
                ], 500);
            }
        } else {
            $order->update($request->only(['status', 'delivery_address'])); // Update field yang diizinkan
        }


        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diperbarui',
            'data'    => $order->fresh()->load(['user', 'orderItems.menu', 'payment', 'delivery.driver', 'review'])
        ], 200);
    }

    public function destroy(Order $order)
    {
        // Menghapus order mungkin tidak disarankan jika sudah ada pembayaran atau pengiriman.
        // Pertimbangkan untuk hanya mengubah status menjadi 'canceled' atau 'archived'.
        // Jika tetap ingin menghapus:
        DB::beginTransaction();
        try {
            // Kembalikan stok jika order belum 'completed' atau 'canceled'
            if (!in_array($order->status, ['completed', 'canceled'])) {
                foreach ($order->orderItems as $item) {
                    $menu = Menu::find($item->menu_id);
                    if ($menu) {
                        $menu->increment('stock', $item->quantity);
                    }
                }
            }
            // Relasi order_items, payment, delivery, review akan terhapus otomatis jika onDelete('cascade') di migrasi.
            $order->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Contoh method tambahan untuk update status saja
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,paid,processing,ready,completed,canceled,failed_payment',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        // Panggil method update utama jika logikanya sama, atau buat logika khusus di sini
        return $this->update($request, $order);
    }
}