@extends('layouts.app')

@section('title', 'Detail Driver: ' . $driver->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Driver: {{ $driver->name }}</h1>
        <div>
            <a href="{{ route('drivers.edit', $driver->driver_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit
            </a>
            <a href="{{ route('drivers.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Driver (ID: {{$driver->driver_id}})</h6>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Driver ID:</dt>
                <dd class="col-sm-9">{{ $driver->driver_id }}</dd>

                <dt class="col-sm-3">Nama:</dt>
                <dd class="col-sm-9">{{ $driver->name }}</dd>

                <dt class="col-sm-3">Telepon:</dt>
                <dd class="col-sm-9">{{ $driver->phone }}</dd>

                <dt class="col-sm-3">Status:</dt>
                <dd class="col-sm-9">
                     @php
                        $statusClass = 'secondary';
                        if ($driver->status == App\Models\Driver::STATUS_AVAILABLE) $statusClass = 'success';
                        if ($driver->status == App\Models\Driver::STATUS_ON_DELIVERY) $statusClass = 'info';
                        if ($driver->status == App\Models\Driver::STATUS_UNAVAILABLE) $statusClass = 'danger';
                    @endphp
                    <span class="badge bg-{{$statusClass}}">{{ App\Models\Driver::getStatuses()[$driver->status] ?? ucfirst($driver->status) }}</span>
                </dd>

                <dt class="col-sm-3">Tipe Kendaraan:</dt>
                <dd class="col-sm-9">{{ App\Models\Driver::getVehicleTypes()[$driver->vehicle_type] ?? ucfirst($driver->vehicle_type) }}</dd>

                <dt class="col-sm-3">Bergabung Sejak:</dt>
                <dd class="col-sm-9">{{ $driver->created_at->format('d M Y, H:i:s') }}</dd>

                <dt class="col-sm-3">Terakhir Diupdate:</dt>
                <dd class="col-sm-9">{{ $driver->updated_at->format('d M Y, H:i:s') }}</dd>
            </dl>

            {{-- Jika Anda ingin menampilkan daftar pengiriman oleh driver ini --}}
            {{-- <hr>
            <h5>Riwayat Pengiriman:</h5>
            @if($driver->deliveries && $driver->deliveries->count() > 0)
                <ul>
                    @foreach($driver->deliveries as $delivery)
                        <li>Order #{{ $delivery->order_id }} - Status: {{ $delivery->status }} pada {{ $delivery->delivery_time }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Belum ada riwayat pengiriman untuk driver ini.</p>
            @endif --}}
        </div>
    </div>
</div>
@endsection