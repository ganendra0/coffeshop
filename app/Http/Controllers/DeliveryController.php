<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Delivery::with(['order.user', 'driver'])->latest();

        if ($request->has('order_id') && $request->order_id != '') {
            $query->where('order_id', $request->order_id);
        }
        if ($request->has('driver_id') && $request->driver_id != '') {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $deliveries = $query->paginate(15);
        $orders = Order::where('order_type', Order::TYPE_DELIVERY) // Hanya order delivery
                        ->orderBy('order_id', 'desc')->get();
        $drivers = Driver::where('status', Driver::STATUS_AVAILABLE) // Hanya driver yang available
                         ->orderBy('name')->get();
        $statuses = Delivery::getStatuses();

        return view('deliveries.index', compact('deliveries', 'orders', 'drivers', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin bisa menugaskan driver ke order delivery.
     */
    public function create(Request $request)
    {
        // Hanya order delivery yang belum punya assignment delivery atau delivery sebelumnya gagal
        $orders = Order::where('order_type', Order::TYPE_DELIVERY)
                        ->where(function ($query) {
                            $query->whereDoesntHave('delivery')
                                  ->orWhereHas('delivery', function($q){
                                      $q->where('status', Delivery::STATUS_FAILED)
                                        ->orWhere('status', Delivery::STATUS_RETURNED);
                                  });
                        })
                        ->orderBy('order_id', 'desc')->get();

        $drivers = Driver::where('status', Driver::STATUS_AVAILABLE)->orderBy('name')->get();
        $selectedOrderId = $request->input('order_id');
        $statuses = Delivery::getStatuses();

        return view('deliveries.create', compact('orders', 'drivers', 'selectedOrderId', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id|unique:deliveries,order_id,NULL,delivery_id,status,!' . Delivery::STATUS_FAILED . ',status,!' . Delivery::STATUS_RETURNED, // Satu delivery aktif per order
            'driver_id' => 'required|exists:drivers,driver_id',
            'status' => ['required', Rule::in(array_keys(Delivery::getStatuses()))],
            'delivery_time' => 'nullable|date',
        ],[
            'order_id.unique' => 'Order ini sudah memiliki data pengiriman aktif. Anda bisa mengeditnya atau menunggu pengiriman sebelumnya gagal/dikembalikan.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('deliveries.create', ['order_id' => $request->order_id])
                        ->withErrors($validator)
                        ->withInput();
        }

        $delivery = Delivery::create($request->all());

        // Update status driver jika ditugaskan
        if ($request->status == Delivery::STATUS_ASSIGNED || $request->status == Delivery::STATUS_ON_THE_WAY) {
            $driver = Driver::find($request->driver_id);
            if ($driver) {
                $driver->status = Driver::STATUS_ON_DELIVERY;
                $driver->save();
            }
        }
        // Update status order
        $order = Order::find($request->order_id);
        if ($order) {
             // Sesuaikan logika update status order berdasarkan status delivery
            if ($request->status == Delivery::STATUS_DELIVERED) {
                $order->status = Order::STATUS_COMPLETED;
            } elseif ($request->status == Delivery::STATUS_ON_THE_WAY) {
                 $order->status = Order::STATUS_DELIVERING;
            } else {
                 $order->status = Order::STATUS_PROCESSING; // Atau status lain
            }
            $order->save();
        }


        return redirect()->route('deliveries.index')->with('success', 'Pengiriman berhasil ditugaskan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery) // Route model binding
    {
        $delivery->load(['order.user', 'driver']);
        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $delivery->load(['order', 'driver']);
        $orders = Order::where('order_id', $delivery->order_id)->get(); // Order tidak diubah
        // Untuk pilihan driver, bisa semua driver atau hanya yang available + driver saat ini
        $drivers = Driver::where('status', Driver::STATUS_AVAILABLE)
                         ->orWhere('driver_id', $delivery->driver_id) // Sertakan driver saat ini
                         ->orderBy('name')->get();
        $statuses = Delivery::getStatuses();
        return view('deliveries.edit', compact('delivery', 'orders', 'drivers', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,driver_id',
            'status' => ['required', Rule::in(array_keys(Delivery::getStatuses()))],
            'delivery_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('deliveries.edit', $delivery->delivery_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $oldDriverId = $delivery->driver_id;
        $oldStatus = $delivery->status;

        $delivery->update($request->all());

        // Update status driver lama jika driver diubah dan status baru masih on_delivery/assigned
        if ($oldDriverId != $request->driver_id && ($request->status == Delivery::STATUS_ASSIGNED || $request->status == Delivery::STATUS_ON_THE_WAY)) {
            $oldDriver = Driver::find($oldDriverId);
            if ($oldDriver) {
                // Cek apakah driver lama masih punya delivery lain yang aktif
                if (!$oldDriver->deliveries()->where('status', Delivery::STATUS_ON_THE_WAY)->where('delivery_id', '!=', $delivery->delivery_id)->exists()) {
                     $oldDriver->status = Driver::STATUS_AVAILABLE;
                     $oldDriver->save();
                }
            }
        }

        // Update status driver baru
        $newDriver = Driver::find($request->driver_id);
        if ($newDriver) {
            if ($request->status == Delivery::STATUS_ASSIGNED || $request->status == Delivery::STATUS_ON_THE_WAY) {
                $newDriver->status = Driver::STATUS_ON_DELIVERY;
            } elseif ($request->status == Delivery::STATUS_DELIVERED || $request->status == Delivery::STATUS_FAILED || $request->status == Delivery::STATUS_RETURNED) {
                 if (!$newDriver->deliveries()->where('status', Delivery::STATUS_ON_THE_WAY)->exists()) {
                    $newDriver->status = Driver::STATUS_AVAILABLE;
                 }
            }
            $newDriver->save();
        }

        // Update status order
        $order = $delivery->order;
        if ($order) {
             if ($request->status == Delivery::STATUS_DELIVERED) {
                $order->status = Order::STATUS_COMPLETED;
            } elseif ($request->status == Delivery::STATUS_ON_THE_WAY) {
                 $order->status = Order::STATUS_DELIVERING;
            } elseif ($oldStatus != $request->status && ($request->status == Delivery::STATUS_FAILED || $request->status == Delivery::STATUS_RETURNED)) {
                // Jika delivery gagal/return, mungkin status order kembali ke processing atau butuh perhatian
                $order->status = Order::STATUS_PROCESSING; // Atau status lain
            }
            $order->save();
        }

        return redirect()->route('deliveries.index')->with('success', 'Data pengiriman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $driver = $delivery->driver;
        $delivery->delete();

        // Update status driver jika sudah tidak ada delivery aktif
        if ($driver && !$driver->deliveries()->where('status', Delivery::STATUS_ON_THE_WAY)->exists()) {
            $driver->status = Driver::STATUS_AVAILABLE;
            $driver->save();
        }

        return redirect()->route('deliveries.index')->with('success', 'Data pengiriman berhasil dihapus.');
    }
}