<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query();
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }
        $drivers = $query->latest('driver_id')->paginate(10);
        return response()->json(['success' => true, 'message' => 'Daftar Data Pengemudi', 'data' => $drivers], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:50',
            'phone'        => 'required|string|max:15|unique:drivers,phone',
            'status'       => 'sometimes|string|in:available,busy,on_delivery,unavailable', // Tambah status jika perlu
            'vehicle_type' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (!$request->filled('status')) {
            $data['status'] = 'available'; // Default status
        }

        $driver = Driver::create($data);
        return response()->json(['success' => true, 'message' => 'Pengemudi berhasil ditambahkan', 'data' => $driver], 201);
    }

    public function show(Driver $driver)
    {
        return response()->json(['success' => true, 'message' => 'Detail Pengemudi', 'data' => $driver->load('deliveries.order')], 200);
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|required|string|max:50',
            'phone'        => 'sometimes|required|string|max:15|unique:drivers,phone,' . $driver->driver_id . ',driver_id',
            'status'       => 'sometimes|required|string|in:available,busy,on_delivery,unavailable',
            'vehicle_type' => 'sometimes|required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $driver->update($request->all());
        return response()->json(['success' => true, 'message' => 'Pengemudi berhasil diperbarui', 'data' => $driver->fresh()], 200);
    }

    public function destroy(Driver $driver)
    {
        // Pertimbangkan apa yang terjadi pada delivery yang sedang di-assign ke driver ini.
        // Sesuai migrasi, delivery.driver_id akan ter-cascade delete atau set null.
        // Jika onDelete('set null'), delivery akan kehilangan driver_id.
        // Jika onDelete('cascade'), delivery record akan dihapus.
        // Mungkin lebih baik tidak mengizinkan delete jika driver punya active deliveries.
        if ($driver->deliveries()->whereNotIn('status', ['delivered', 'failed'])->exists()) {
             return response()->json(['success' => false, 'message' => 'Pengemudi memiliki pengiriman aktif, tidak dapat dihapus.'], 400);
        }

        $driver->delete();
        return response()->json(['success' => true, 'message' => 'Pengemudi berhasil dihapus'], 200);
    }
}