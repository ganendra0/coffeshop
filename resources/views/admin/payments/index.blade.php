@extends('layouts.admin')

{{-- Definisikan variabel untuk judul berdasarkan filter saat ini --}}
@php
    $currentOrderIdFromRequest = request()->get('order_id'); // Ambil order_id dari URL jika ada
    $currentStatusFromRequest = request()->get('status');    // Ambil status dari URL jika ada

    $pageTitleForView = 'Daftar Pembayaran'; // Judul default
    if ($currentOrderIdFromRequest) {
        $pageTitleForView .= ' untuk Order #' . $currentOrderIdFromRequest;
    }
    if ($currentStatusFromRequest) {
        $pageTitleForView .= ' (Status: ' . ucfirst($currentStatusFromRequest) . ')';
    }
@endphp

{{-- Gunakan variabel yang sudah didefinisikan di atas untuk section title --}}
@section('title', $pageTitleForView)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        {{-- Gunakan variabel yang sudah didefinisikan di atas untuk H1 --}}
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitleForView }}</h1>
        {{-- Tombol "Catat Pembayaran" bisa mengirim parameter order_id jika ada filter aktif --}}
        <a href="{{ route('payments.create', $currentOrderIdFromRequest ? ['order_id' => $currentOrderIdFromRequest] : []) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Catat Pembayaran
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            {{-- Form filter --}}
            <form action="{{ route('payments.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pembayaran</h6>
                </div>
                <div class="col-md-3">
                    <select name="order_id" class="form-select form-select-sm">
                        <option value="">Semua Order</option>
                        @foreach($orders as $order) {{-- $orders dikirim dari PaymentController@index --}}
                            <option value="{{ $order->order_id }}" {{ $currentOrderIdFromRequest == $order->order_id ? 'selected' : '' }}>
                                Order #{{ $order->order_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                     <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label) {{-- $statuses dikirim dari PaymentController@index --}}
                            <option value="{{ $value }}" {{ $currentStatusFromRequest == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            {{-- ... sisa tabel Anda ... --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>Jumlah (Rp)</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Waktu Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment) {{-- $payments dikirim dari PaymentController@index --}}
                        <tr>
                            <td>{{ $payment->payment_id }}</td>
                            <td>
                                <a href="{{ $payment->order ? route('orders.show', $payment->order_id) : '#' }}">#{{ $payment->order_id }}</a>
                            </td>
                            <td>{{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    if ($payment->status == App\Models\Payment::STATUS_PENDING) $statusClass = 'warning text-dark';
                                    if ($payment->status == App\Models\Payment::STATUS_PAID) $statusClass = 'success';
                                    if ($payment->status == App\Models\Payment::STATUS_FAILED) $statusClass = 'danger';
                                    if ($payment->status == App\Models\Payment::STATUS_REFUNDED) $statusClass = 'info';
                                @endphp
                                <span class="badge bg-{{$statusClass}}">{{ ucfirst($payment->status) }}</span>
                            </td>
                            <td>
                                {{-- Pastikan accessor payment_proof_url ada di model Payment --}}
                                @if($payment->payment_proof_url)
                                    <a href="{{ $payment->payment_proof_url }}" target="_blank">
                                        <img src="{{ $payment->payment_proof_url }}" alt="Bukti" width="60" class="img-thumbnail">
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $payment->payment_time ? $payment->payment_time->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('payments.show', $payment->payment_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('payments.edit', $payment->payment_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deletePaymentModal-{{ $payment->payment_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                {{-- Modal Delete --}}
                                <div class="modal fade" id="deletePaymentModal-{{ $payment->payment_id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">Apakah Anda yakin ingin menghapus data pembayaran untuk Order #{{ $payment->order_id }}?</div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" style="display:inline;">@csrf @method('DELETE') <button type="submit" class="btn btn-danger">Hapus</button></form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="mt-3">
                {{ $payments->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection