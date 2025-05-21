<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Index bisa untuk melihat semua payment, atau per order
    public function index(Request $request)
    {
        $query = Payment::with('order.user');

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest('payment_id')->paginate(10);
        return response()->json(['success' => true, 'message' => 'Daftar Data Pembayaran', 'data' => $payments], 200);
    }

    // Store payment biasanya terjadi saat order dibuat (jika bukan cash) atau saat user upload bukti
    // Method ini lebih untuk admin/sistem membuat record payment secara manual jika diperlukan.
    // Untuk user upload bukti, biasanya ada endpoint khusus.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'      => 'required|exists:orders,order_id',
            'amount'        => 'required|numeric|min:0',
            'status'        => 'required|string|in:pending,success,failed,canceled',
            'payment_proof_file' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048', // 'payment_proof_file'
            'payment_time'  => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);
        // Cek apakah order sudah ada payment record
        if ($order->payment && $order->payment->status === 'success') {
             return response()->json(['success' => false, 'message' => 'Pesanan ini sudah memiliki pembayaran yang berhasil.'], 400);
        }

        DB::beginTransaction();
        try {
            $data = $request->except('payment_proof_file');
            if ($request->hasFile('payment_proof_file')) {
                $filePath = $request->file('payment_proof_file')->store('payment_proofs', 'public');
                $data['payment_proof'] = $filePath;
            }

            if (empty($request->payment_time) && $request->status === 'success') {
                $data['payment_time'] = now();
            }

            // Jika ada payment sebelumnya (misal gagal/pending), update. Jika tidak, buat baru.
            $payment = $order->payment()->updateOrCreate(
                ['order_id' => $order->order_id], // Kunci untuk mencari atau membuat
                $data // Nilai untuk diupdate atau dibuat
            );


            // Jika pembayaran sukses, update status order menjadi 'paid'
            if ($payment->status === 'success' && $order->status === 'pending') { // atau status lain yang menunggu pembayaran
                $order->update(['status' => 'paid']);
            } elseif ($payment->status === 'failed' && $order->status === 'pending') {
                $order->update(['status' => 'failed_payment']); // Buat status baru jika perlu
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat/diperbarui',
                'data' => $payment->load('order')
            ], $payment->wasRecentlyCreated ? 201 : 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mencatat pembayaran: ' . $e->getMessage()], 500);
        }
    }

    public function show(Payment $payment)
    {
        // if ($payment->payment_proof) {
        //     $payment->payment_proof_url = Storage::disk('public')->url($payment->payment_proof);
        // }
        return response()->json([
            'success' => true,
            'message' => 'Detail Pembayaran',
            'data'    => $payment->load('order.user') // Load juga data order
        ], 200);
    }

    // Endpoint khusus untuk user upload bukti bayar
    public function uploadProof(Request $request, Order $order)
    {
        if (!$order->payment || $order->payment->status === 'success') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak memerlukan bukti pembayaran atau sudah lunas.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_proof_file' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $payment = $order->payment;
            // Hapus bukti lama jika ada
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }

            $filePath = $request->file('payment_proof_file')->store('payment_proofs', 'public');

            $payment->update([
                'payment_proof' => $filePath,
                'status' => 'pending', // Atau 'waiting_confirmation' jika ada alur verifikasi manual
                // 'payment_time' => now(), // Bisa diisi di sini atau saat admin konfirmasi
            ]);

            // Update status order jika perlu (misal jadi 'waiting_confirmation')
            // $order->update(['status' => 'waiting_confirmation']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah.',
                'data' => $payment->fresh()->load('order')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mengunggah bukti: ' . $e->getMessage()], 500);
        }
    }


    // Update biasanya untuk admin mengkonfirmasi atau mengubah status payment
    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'status'       => 'sometimes|required|string|in:pending,success,failed,canceled',
            'payment_time' => 'nullable|date_format:Y-m-d H:i:s',
            // 'amount' bisa diupdate admin jika ada kesalahan, tapi hati-hati
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataToUpdate = $request->only(['status', 'payment_time']);
            $order = $payment->order;

            // Jika status baru 'success' dan payment_time belum ada, set ke now()
            if ($request->filled('status') && $request->status === 'success' && empty($request->payment_time) && empty($payment->payment_time)) {
                $dataToUpdate['payment_time'] = now();
            }

            $payment->update($dataToUpdate);

            // Sinkronisasi status order
            if ($payment->status === 'success' && $order->status !== 'paid' && $order->status !== 'processing' && $order->status !== 'ready' && $order->status !== 'completed') {
                $order->update(['status' => 'paid']);
            } elseif ($payment->status === 'failed' && $order->status === 'pending') {
                $order->update(['status' => 'failed_payment']);
            } elseif ($payment->status === 'canceled' && in_array($order->status, ['pending', 'failed_payment'])) {
                // Jika payment dicancel, dan order masih pending/failed, order juga bisa dicancel
                // $order->update(['status' => 'canceled']);
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui',
                'data'    => $payment->fresh()->load('order.user')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pembayaran: ' . $e->getMessage()], 500);
        }
    }

    // Destroy payment record (hati-hati)
    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Hapus file bukti pembayaran jika ada
            if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            // Mungkin set status order kembali ke 'pending' jika payment dihapus dan order belum diproses jauh
            $order = $payment->order;
            if ($order && $order->status === 'paid' && $payment->status === 'success') {
                 // $order->update(['status' => 'pending']); // Atau status lain
            }

            $payment->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data pembayaran berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pembayaran: ' . $e->getMessage()], 500);
        }
    }
}