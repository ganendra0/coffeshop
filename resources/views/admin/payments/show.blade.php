@extends('layouts.admin')

@section('title', 'Detail Pembayaran Order #' . $payment->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran (ID: {{ $payment->payment_id }})</h1>
        <div>
            <a href="{{ route('payments.edit', $payment->payment_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit
            </a>
            <a href="{{ route('payments.index', ['order_id' => $payment->order_id]) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
            </a>
        </div>
    </div>

     @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <dl class="row">
                        <dt class="col-sm-4">Payment ID:</dt>
                        <dd class="col-sm-8">{{ $payment->payment_id }}</dd>

                        <dt class="col-sm-4">Order ID:</dt>
                        <dd class="col-sm-8"><a href="{{ route('orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></dd>

                        <dt class="col-sm-4">Pelanggan:</dt>
                        <dd class="col-sm-8">{{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}</dd>

                        <dt class="col-sm-4">Jumlah Dibayar:</dt>
                        <dd class="col-sm-8">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @php
                                $statusClass = 'secondary';
                                if ($payment->status == App\Models\Payment::STATUS_PENDING) $statusClass = 'warning text-dark';
                                if ($payment->status == App\Models\Payment::STATUS_PAID) $statusClass = 'success';
                                if ($payment->status == App\Models\Payment::STATUS_FAILED) $statusClass = 'danger';
                                if ($payment->status == App\Models\Payment::STATUS_REFUNDED) $statusClass = 'info';
                            @endphp
                            <span class="badge bg-{{$statusClass}}">{{ ucfirst($payment->status) }}</span>
                        </dd>

                        <dt class="col-sm-4">Waktu Pembayaran:</dt>
                        <dd class="col-sm-8">{{ $payment->payment_time ? $payment->payment_time->format('d M Y, H:i:s') : '-' }}</dd>

                        <dt class="col-sm-4">Dicatat Pada:</dt>
                        <dd class="col-sm-8">{{ $payment->created_at->format('d M Y, H:i:s') }}</dd>

                         <dt class="col-sm-4">Diupdate Pada:</dt>
                        <dd class="col-sm-8">{{ $payment->updated_at->format('d M Y, H:i:s') }}</dd>
                    </dl>
                </div>
                 <div class="col-md-5">
                    <h6 class="mb-3">Bukti Pembayaran:</h6>
                    @if($payment->payment_proof_url)
                        <a href="{{ $payment->payment_proof_url }}" target="_blank">
                            <img src="{{ $payment->payment_proof_url }}" alt="Bukti Pembayaran" class="img-fluid img-thumbnail" style="max-height: 400px;">
                        </a>
                    @else
                        <p class="text-muted">Tidak ada bukti pembayaran diunggah.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection