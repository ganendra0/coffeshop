@extends('layouts.app')

@section('title', 'Detail Pengiriman Order #' . $delivery->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengiriman (ID: {{$delivery->delivery_id}})</h1>
        <div>
            <a href="{{ route('deliveries.edit', $delivery->delivery_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit
            </a>
            <a href="{{ route('deliveries.index', ['order_id' => $delivery->order_id]) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengiriman</h6>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Delivery ID:</dt>
                <dd class="col-sm-9">{{ $delivery->delivery_id }}</dd>

                <dt class="col-sm-3">Order ID:</dt>
                <dd class="col-sm-9"><a href="{{ $delivery->order ? route('orders.show', $delivery->order_id) : '#' }}">#{{ $delivery->order_id }}</a></dd>

                <dt class="col-sm-3">Pelanggan:</dt>
                <dd class="col-sm-9">{{ $delivery->order && $delivery->order->user ? $delivery->order->user->name : 'N/A' }}</dd>

                <dt class="col-sm-3">Alamat Kirim:</dt>
                <dd class="col-sm-9">{{ $delivery->order ? $delivery->order->delivery_address : 'N/A' }}</dd>

                <dt class="col-sm-3">Driver Ditugaskan:</dt>
                <dd class="col-sm-9">{{ $delivery->driver ? $delivery->driver->name . ' (' . $delivery->driver->vehicle_type . ')' : 'Belum Ditugaskan' }}</dd>

                <dt class="col-sm-3">Status Pengiriman:</dt>
                <dd class="col-sm-9">
                     @php
                        $statusClass = 'secondary';
                        if ($delivery->status == App\Models\Delivery::STATUS_ASSIGNED) $statusClass = 'primary';
                        if ($delivery->status == App\Models\Delivery::STATUS_ON_THE_WAY) $statusClass = 'info';
                        if ($delivery->status == App\Models\Delivery::STATUS_DELIVERED) $statusClass = 'success';
                        if ($delivery->status == App\Models\Delivery::STATUS_FAILED) $statusClass = 'danger';
                        if ($delivery->status == App\Models\Delivery::STATUS_RETURNED) $statusClass = 'warning text-dark';
                    @endphp
                    <span class="badge bg-{{$statusClass}}">{{ App\Models\Delivery::getStatuses()[$delivery->status] ?? ucfirst($delivery->status) }}</span>
                </dd>

                <dt class="col-sm-3">Waktu Aktual Pengiriman:</dt>
                <dd class="col-sm-9">{{ $delivery->delivery_time ? $delivery->delivery_time->format('d M Y, H:i:s') : 'Belum Terkirim' }}</dd>

                <dt class="col-sm-3">Ditugaskan Pada:</dt>
                <dd class="col-sm-9">{{ $delivery->created_at->format('d M Y, H:i:s') }}</dd>

                <dt class="col-sm-3">Terakhir Diupdate:</dt>
                <dd class="col-sm-9">{{ $delivery->updated_at->format('d M Y, H:i:s') }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection