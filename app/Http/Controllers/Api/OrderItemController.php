<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class OrderItemController extends Controller
{
    // Biasanya tidak ada index untuk semua order item, tapi mungkin per order
    public function index(Order $order) // Contoh: /api/v1/orders/{order}/items
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar Item untuk Pesanan ' . $order->order_id,
            'data'    => $order->orderItems()->with('menu')->get()
        ], 200);
    }

    // Store biasanya terjadi saat membuat Order. Ini contoh jika ingin menambah item ke order yang sudah ada
    public function store(Request $request, Order $order)
    {
        // Pastikan order masih dalam status yang memungkinkan penambahan item (misal 'pending')
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menambah item ke pesanan dengan status ' . $order->status], 400);
        }

        $validator = Validator::make($request->all(), [
            'menu_id'  => 'required|exists:menus,menu_id',
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $menu = Menu::find($request->menu_id);
        if (!$menu || !$menu->is_available || $menu->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Menu tidak tersedia atau stok tidak cukup.'], 400);
        }

        DB::beginTransaction();
        try {
            $orderItem = $order->orderItems()->create([
                'menu_id'  => $menu->menu_id,
                'quantity' => $request->quantity,
                'notes'    => $request->notes,
            ]);

            // Kurangi stok menu
            $menu->decrement('stock', $request->quantity);

            // Update total_price di order
            $order->total_price += ($menu->price * $request->quantity);
            $order->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke pesanan',
                'data'    => $orderItem->load('menu')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambah item: ' . $e->getMessage()], 500);
        }
    }

    public function show(OrderItem $orderItem) // Atau OrderItem $item jika nama parameter di route adalah 'item'
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Item Pesanan',
            'data'    => $orderItem->load('menu', 'order')
        ], 200);
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $order = $orderItem->order;
        // Pastikan order masih dalam status yang memungkinkan perubahan item
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengubah item pada pesanan dengan status ' . $order->status], 400);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $menu = $orderItem->menu; // Menu yang terkait dengan item ini
            $oldQuantity = $orderItem->quantity;
            $newQuantity = $request->filled('quantity') ? $request->quantity : $oldQuantity;
            $quantityDifference = $newQuantity - $oldQuantity;

            // Cek stok jika kuantitas bertambah
            if ($quantityDifference > 0 && (!$menu || !$menu->is_available || $menu->stock < $quantityDifference)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Stok menu tidak cukup untuk menambah kuantitas.'], 400);
            }

            $orderItem->update($request->only(['quantity', 'notes']));

            // Update stok menu
            if ($quantityDifference !== 0) {
                $menu->stock -= $quantityDifference; // Bisa jadi negatif jika mengurangi, jadi decrement/increment lebih aman
                // atau:
                // if ($quantityDifference > 0) $menu->decrement('stock', $quantityDifference);
                // else $menu->increment('stock', abs($quantityDifference));
                $menu->save();
            }


            // Update total_price di order
            $order->total_price += ($menu->price * $quantityDifference);
            $order->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Item pesanan berhasil diperbarui',
                'data'    => $orderItem->fresh()->load('menu')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui item: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        // Pastikan order masih dalam status yang memungkinkan penghapusan item
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus item dari pesanan dengan status ' . $order->status], 400);
        }

        DB::beginTransaction();
        try {
            $menu = $orderItem->menu;
            $quantity = $orderItem->quantity;

            // Kembalikan stok menu
            if ($menu) {
                $menu->increment('stock', $quantity);
            }

            // Kurangi total_price di order
            $order->total_price -= ($menu->price * $quantity);
            $order->save();

            $orderItem->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item pesanan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
        }
    }
}