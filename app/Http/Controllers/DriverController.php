<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi enum/in

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Driver::latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('vehicle_type') && $request->vehicle_type != '') {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        $drivers = $query->paginate(15);
        $statuses = Driver::getStatuses();
        $vehicleTypes = Driver::getVehicleTypes(); // Ambil dari model

        return view('drivers.index', compact('drivers', 'statuses', 'vehicleTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Driver::getStatuses();
        $vehicleTypes = Driver::getVehicleTypes();
        return view('drivers.create', compact('statuses', 'vehicleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:15|unique:drivers,phone',
            'status' => ['required', Rule::in(array_keys(Driver::getStatuses()))],
            'vehicle_type' => ['required', Rule::in(array_keys(Driver::getVehicleTypes()))],
        ]);

        if ($validator->fails()) {
            return redirect()->route('drivers.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Driver::create($request->all());

        return redirect()->route('drivers.index')->with('success', 'Driver baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver) // Route model binding
    {
        // $driver->load('deliveries'); // Jika ingin menampilkan daftar pengiriman oleh driver
        return view('drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        $statuses = Driver::getStatuses();
        $vehicleTypes = Driver::getVehicleTypes();
        return view('drivers.edit', compact('driver', 'statuses', 'vehicleTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:15|unique:drivers,phone,' . $driver->driver_id . ',driver_id', // Abaikan driver saat ini
            'status' => ['required', Rule::in(array_keys(Driver::getStatuses()))],
            'vehicle_type' => ['required', Rule::in(array_keys(Driver::getVehicleTypes()))],
        ]);

        if ($validator->fails()) {
            return redirect()->route('drivers.edit', $driver->driver_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $driver->update($request->all());

        return redirect()->route('drivers.index')->with('success', 'Data driver berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        // Tambahkan logika jika driver punya pengiriman aktif, mungkin tidak boleh dihapus
        // if ($driver->deliveries()->where('status', '!=', Delivery::STATUS_COMPLETED)->count() > 0) {
        //     return redirect()->route('drivers.index')->with('error', 'Driver tidak bisa dihapus karena memiliki pengiriman aktif.');
        // }
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver berhasil dihapus.');
    }
}