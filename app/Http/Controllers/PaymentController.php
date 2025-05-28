<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi enum

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = Payment::with('order.user')->latest();

    $currentOrderId = $request->input('order_id'); // Digunakan untuk filter query
    $currentStatus = $request->input('status');  // Digunakan untuk filter query

    if ($currentOrderId && $currentOrderId != '') {
        $query->where('order_id', $currentOrderId);
    }
    if ($currentStatus && $currentStatus != '') {
        $query->where('status', $currentStatus);
    }

    $payments = $query->paginate(15);
    $orders = Order::orderBy('order_id', 'desc')->get();
    $statuses = Payment::getStatuses();

    // Tidak perlu mengirim $selectedOrderId dari sini,
    // karena view index menggunakan request()->get('order_id') atau $currentOrderIdFromRequest
    // untuk logika tampilannya sendiri.
    return view('payments.index', compact('payments', 'orders', 'statuses'));
}

    /**
     * Show the form for creating a new resource.
     * (Biasanya payment dibuat terkait order tertentu, bukan dari halaman create general)
     */
    public function create(Request $request)
    {
        $orders = Order::whereDoesntHave('payment') // Hanya order yang belum ada payment record
                        ->orWhereHas('payment', function($q){
                            $q->where('status', Payment::STATUS_FAILED); // Atau payment sebelumnya gagal
                        })
                        ->orderBy('order_id', 'desc')->get();

        $selectedOrderId = $request->input('order_id');
        $statuses = Payment::getStatuses();

        if ($selectedOrderId) {
            $order = Order::find($selectedOrderId);
            if ($order && $order->payment && $order->payment->status !== Payment::STATUS_FAILED) {
                // Jika order sudah ada payment yang tidak gagal, redirect atau beri pesan
                return redirect()->route('payments.index')->with('error', 'Order #'.$selectedOrderId.' sudah memiliki pembayaran yang aktif.');
            }
        }


        return view('payments.create', compact('orders', 'selectedOrderId', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,order_id|unique:payments,order_id,NULL,payment_id,status,!' . Payment::STATUS_FAILED, // Satu payment aktif per order
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(array_keys(Payment::getStatuses()))],
            'payment_proof_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Input file
            'payment_time' => 'nullable|date',
        ], [
            'order_id.unique' => 'Order ini sudah memiliki data pembayaran aktif. Anda bisa mengeditnya atau menunggu pembayaran sebelumnya gagal.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('payments.create', ['order_id' => $request->order_id])
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->except('payment_proof_file'); // Semua kecuali file

        if ($request->hasFile('payment_proof_file')) {
            // Simpan file ke 'storage/app/public/payment_proofs'
            $filePath = $request->file('payment_proof_file')->store('payment_proofs', 'public');
            $data['payment_proof'] = $filePath; // Simpan path relatif
        }

        Payment::create($data);

        // Opsional: Update status Order induk
        $order = Order::find($request->order_id);
        if ($order && $request->status == Payment::STATUS_PAID) {
            // Sesuaikan logika update status order
            // $order->status = Order::STATUS_PROCESSING; // Atau status lain
            // $order->save();
        }

        return redirect()->route('payments.index', ['order_id' => $request->order_id])->with('success', 'Data pembayaran berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment) // Menggunakan nama parameter 'payment'
    {
        $payment->load('order.user');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $payment->load('order');
        // Biasanya order_id tidak diubah untuk payment yang ada
        $orders = Order::where('order_id', $payment->order_id)->get(); // Hanya order terkait
        $statuses = Payment::getStatuses();
        return view('payments.edit', compact('payment', 'orders', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            // order_id biasanya tidak diubah
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(array_keys(Payment::getStatuses()))],
            'payment_proof_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('payments.edit', $payment->payment_id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->except('payment_proof_file');

        if ($request->hasFile('payment_proof_file')) {
            // Hapus bukti lama jika ada
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $filePath = $request->file('payment_proof_file')->store('payment_proofs', 'public');
            $data['payment_proof'] = $filePath;
        }

        $payment->update($data);

        // Opsional: Update status Order induk
        if ($payment->order && $request->status == Payment::STATUS_PAID) {
            // Sesuaikan logika update status order
            // $payment->order->status = Order::STATUS_PROCESSING;
            // $payment->order->save();
        } elseif ($payment->order && $request->status == Payment::STATUS_FAILED) {
            // $payment->order->status = Order::STATUS_PENDING; // Kembalikan ke pending jika gagal
            // $payment->order->save();
        }


        return redirect()->route('payments.index', ['order_id' => $payment->order_id])->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $orderId = $payment->order_id;
        // Hapus bukti pembayaran dari storage
        if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
            Storage::disk('public')->delete($payment->payment_proof);
        }
        $payment->delete();

        // Opsional: Update status Order induk
        // $order = Order::find($orderId);
        // if($order){
        //     $order->status = Order::STATUS_PENDING; // Kembalikan ke pending
        //     $order->save();
        // }

        return redirect()->route('payments.index', ['order_id' => $orderId])->with('success', 'Data pembayaran berhasil dihapus.');
    }
}