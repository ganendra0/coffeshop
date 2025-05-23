<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User; // Untuk mengambil daftar user
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
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get(); // Ambil semua user untuk dropdown
        $orderTypes = Order::getOrderTypes();
        $statuses = Order::getStatuses();
        // Contoh metode pembayaran
        $paymentMethods = ['Cash', 'Debit Card', 'Credit Card', 'E-Wallet'];

        return view('orders.create', compact('users', 'orderTypes', 'statuses', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,user_id', // user_id boleh null, tapi jika diisi harus ada di tabel users
            'order_type' => ['required', Rule::in(array_keys(Order::getOrderTypes()))],
            'status' => ['required', Rule::in(array_keys(Order::getStatuses()))],
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:20',
            'delivery_address' => 'nullable|string|required_if:order_type,'.Order::TYPE_DELIVERY, // Wajib jika order_type = delivery
        ]);

        if ($validator->fails()) {
            return redirect()->route('orders.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Order::create($request->all());

        return redirect()->route('orders.index')->with('success', 'Order baru berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order_id) // Route Model Binding
    {
        // Eager load user dan items jika ada
        $order_id->load('user', 'items');
        return view('orders.show', ['order' => $order_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order_id) // Route Model Binding
    {
        $users = User::orderBy('name')->get();
        $orderTypes = Order::getOrderTypes();
        $statuses = Order::getStatuses();
        $paymentMethods = ['Cash', 'Debit Card', 'Credit Card', 'E-Wallet'];

        return view('orders.edit', compact('order', 'users', 'orderTypes', 'statuses', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order_id) // Route Model Binding
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,user_id',
            'order_type' => ['required', Rule::in(array_keys(Order::getOrderTypes()))],
            'status' => ['required', Rule::in(array_keys(Order::getStatuses()))],
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:20',
            'delivery_address' => 'nullable|string|required_if:order_type,'.Order::TYPE_DELIVERY,
        ]);

        if ($validator->fails()) {
            return redirect()->route('orders.edit', $order_id->order_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $order_id->update($request->all());

        return redirect()->route('orders.index')->with('success', 'Data order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order_id) // Route Model Binding
    {
        // Tambahkan logika jika order punya item, mungkin tidak boleh dihapus langsung
        // if ($order_id->items && $order_id->items()->count() > 0) {
        //     return redirect()->route('orders.index')->with('error', 'Order tidak bisa dihapus karena memiliki item.');
        // }
        $order_id->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }
}