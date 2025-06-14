<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // HANYA MENGIRIM DATA YANG DIPERLUKAN
        $users = User::orderBy('name')->get();
        $statuses = Order::getStatuses();

        return view('admin.orders.create', compact('users', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // VALIDASI TANPA ORDER_TYPE DAN DELIVERY_ADDRESS
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,user_id',
            'status' => ['required', Rule::in(array_keys(Order::getStatuses()))],
            'total_price' => 'required|numeric|min:0',
            'notes_for_restaurant' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('orders.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['user_id', 'status', 'total_price', 'notes_for_restaurant']);
        // Menetapkan nilai default untuk order_type jika kolomnya masih ada di DB
        $data['order_type'] = 'pickup'; // Atau nilai default apapun

        Order::create($data);

        return redirect()->route('orders.index')->with('success', 'Order baru berhasil dibuat.');
    }

    // ... method show, edit, update, destroy juga perlu disederhanakan ...

    public function edit(Order $order)
    {
        // HANYA MENGIRIM DATA YANG DIPERLUKAN
        $users = User::orderBy('name')->get();
        $statuses = Order::getStatuses();

        return view('admin.orders.edit', compact('order', 'users', 'statuses'));
    }


    public function update(Request $request, Order $order)
    {
        // VALIDASI TANPA ORDER_TYPE DAN DELIVERY_ADDRESS
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,user_id',
            'status' => ['required', Rule::in(array_keys(Order::getStatuses()))],
            'total_price' => 'required|numeric|min:0',
            'notes_for_restaurant' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('orders.edit', $order->order_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only(['user_id', 'status', 'total_price', 'notes_for_restaurant']);
        $order->update($data);

        return redirect()->route('orders.index')->with('success', 'Data order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        if ($order->orderItems()->count() > 0) {
            return redirect()->route('admin.orders.index')->with('error', 'Order tidak bisa dihapus karena memiliki item pesanan.');
        }

        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus.');
    }
}