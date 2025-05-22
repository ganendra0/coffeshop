<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['order.user', 'driver']);
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $deliveries = $query->latest('delivery_id')->paginate(10);
        return response()->json(['success' => true, 'message' => 'Daftar Data Pengiriman', 'data' => $deliveries], 200);
    }

    // Membuat record delivery (assign driver ke order)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'  => 'required|exists:orders,order_id|unique:deliveries,order_id', // Satu order hanya satu delivery record
            'driver_id' => 'required|exists:drivers,driver_id',
            'status'    => 'sometimes|string|in:assigned,on_the_way,delivered,failed', // 'pending_assignment' bisa jadi status awal order
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);
        if ($order->order_type !== 'delivery') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini bukan tipe pengiriman.'], 400);
        }
        if (!in_array($order->status, ['paid', 'processing', 'ready'])) { // Hanya order yang sudah bayar/diproses/siap
            return response()->json(['success' => false, 'message' => 'Pesanan belum siap untuk pengiriman (status: '.$order->status.').'], 400);
        }

        $driver = Driver::find($request->driver_id);
        if ($driver->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'Pengemudi tidak tersedia (status: '.$driver->status.').'], 400);
        }


        DB::beginTransaction();
        try {
            $data = $request->all();
            if (!$request->filled('status')) {
                $data['status'] = 'assigned';
            }

            $delivery = Delivery::create($data);

            // Update status driver menjadi 'on_delivery' atau 'busy'
            $driver->update(['status' => 'on_delivery']);
            // Update status order menjadi 'processing' atau 'on_the_way'
            if ($order->status === 'ready' || $order->status === 'paid') {
                 $order->update(['status' => 'on_the_way']); // Asumsi status baru
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil dibuat/di-assign',
                'data' => $delivery->load(['order.user', 'driver'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membuat pengiriman: ' . $e->getMessage()], 500);
        }
    }

    public function show(Delivery $delivery)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Pengiriman',
            'data'    => $delivery->load(['order.user', 'driver'])
        ], 200);
    }

    // Update status pengiriman (misal oleh driver atau admin)
    public function update(Request $request, Delivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'status'       => 'sometimes|required|string|in:assigned,on_the_way,delivered,failed',
            'driver_id'    => 'sometimes|required|exists:drivers,driver_id', // Jika mau re-assign driver
            // 'delivery_time' akan di-set otomatis jika status 'delivered'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataToUpdate = $request->only(['status', 'driver_id']);
            $oldStatus = $delivery->status;
            $newStatus = $request->input('status', $oldStatus);

            // Jika status baru 'delivered' dan delivery_time belum ada, set ke now()
            if ($newStatus === 'delivered' && empty($delivery->delivery_time)) {
                $dataToUpdate['delivery_time'] = now();
            } elseif ($newStatus !== 'delivered') {
                $dataToUpdate['delivery_time'] = null; // Reset jika status berubah dari delivered
            }

            // Handle perubahan driver
            $oldDriverId = $delivery->driver_id;
            $newDriverId = $request->input('driver_id', $oldDriverId);

            if ($newDriverId !== $oldDriverId) {
                // Cek driver baru
                $newDriver = Driver::find($newDriverId);
                if (!$newDriver || $newDriver->status !== 'available') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Driver baru tidak tersedia.'], 400);
                }
                // Set driver lama jadi available (jika masih on_delivery olehnya)
                $oldDriver = Driver::find($oldDriverId);
                if ($oldDriver && $oldDriver->status === 'on_delivery') {
                    $oldDriver->update(['status' => 'available']);
                }
                // Set driver baru jadi on_delivery
                $newDriver->update(['status' => 'on_delivery']);
                $dataToUpdate['driver_id'] = $newDriverId;
            }


            $delivery->update($dataToUpdate);

            // Update status order dan driver berdasarkan status delivery
            $order = $delivery->order;
            $currentDriver = Driver::find($delivery->driver_id); // Driver yang terassign saat ini

            if ($newStatus === 'delivered') {
                if ($order->status !== 'completed') $order->update(['status' => 'completed']);
                if ($currentDriver && $currentDriver->status === 'on_delivery') $currentDriver->update(['status' => 'available']);
            } elseif ($newStatus === 'failed') {
                // if ($order->status !== 'failed_delivery') $order->update(['status' => 'failed_delivery']); // Status baru jika perlu
                if ($currentDriver && $currentDriver->status === 'on_delivery') $currentDriver->update(['status' => 'available']);
            } elseif ($newStatus === 'on_the_way') {
                if ($order->status !== 'on_the_way') $order->update(['status' => 'on_the_way']);
                if ($currentDriver && $currentDriver->status !== 'on_delivery') $currentDriver->update(['status' => 'on_delivery']);
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman berhasil diperbarui',
                'data'    => $delivery->fresh()->load(['order.user', 'driver'])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pengiriman: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Delivery $delivery)
    {
        // Menghapus record delivery (misal karena salah assign)
        DB::beginTransaction();
        try {
            $order = $delivery->order;
            $driver = $delivery->driver;

            // Jika delivery dibatalkan, kembalikan status driver dan order
            if ($driver && $driver->status === 'on_delivery' && $delivery->status !== 'delivered' && $delivery->status !== 'failed') {
                $driver->update(['status' => 'available']);
            }
            if ($order && $order->status === 'on_the_way' && $delivery->status !== 'delivered' && $delivery->status !== 'failed') {
                 // $order->update(['status' => 'ready']); // Atau status sebelumnya
            }

            $delivery->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data pengiriman berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pengiriman: ' . $e->getMessage()], 500);
        }
    }
}