@extends('layouts.app')

@section('title', 'Manajemen Pengiriman')

@php
    $currentOrderId = request()->get('order_id');
    $currentDriverId = request()->get('driver_id');
    $currentStatus = request()->get('status');

    $pageTitle = 'Daftar Pengiriman';
    if ($currentOrderId) $pageTitle .= ' Order #' . $currentOrderId;
    if ($currentDriverId) {
        $driver = \App\Models\Driver::find($currentDriverId);
        $pageTitle .= ' oleh ' . ($driver ? $driver->name : 'Driver ID ' . $currentDriverId);
    }
    if ($currentStatus) $pageTitle .= ' (Status: ' . ($statuses[$currentStatus] ?? ucfirst($currentStatus)) . ')';
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('deliveries.create', $currentOrderId ? ['order_id' => $currentOrderId] : []) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tugaskan Pengiriman
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <form action="{{ route('deliveries.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pengiriman</h6>
                </div>
                <div class="col-md-3">
                    <select name="order_id" class="form-select form-select-sm">
                        <option value="">Semua Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->order_id }}" {{ $currentOrderId == $order->order_id ? 'selected' : '' }}>
                                Order #{{ $order->order_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                 <div class="col-md-3">
                    <select name="driver_id" class="form-select form-select-sm">
                        <option value="">Semua Driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->driver_id }}" {{ $currentDriverId == $driver->driver_id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                     <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $currentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Waktu Kirim</th>
                            <th>Ditugaskan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->delivery_id }}</td>
                            <td><a href="{{ $delivery->order ? route('orders.show', $delivery->order_id) : '#' }}">#{{ $delivery->order_id }}</a></td>
                            <td>{{ $delivery->order && $delivery->order->user ? $delivery->order->user->name : 'N/A' }}</td>
                            <td>{{ $delivery->driver ? $delivery->driver->name : 'Belum Ditugaskan' }}</td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    if ($delivery->status == App\Models\Delivery::STATUS_ASSIGNED) $statusClass = 'primary';
                                    if ($delivery->status == App\Models\Delivery::STATUS_ON_THE_WAY) $statusClass = 'info';
                                    if ($delivery->status == App\Models\Delivery::STATUS_DELIVERED) $statusClass = 'success';
                                    if ($delivery->status == App\Models\Delivery::STATUS_FAILED) $statusClass = 'danger';
                                    if ($delivery->status == App\Models\Delivery::STATUS_RETURNED) $statusClass = 'warning text-dark';
                                @endphp
                                <span class="badge bg-{{$statusClass}}">{{ $statuses[$delivery->status] ?? ucfirst($delivery->status) }}</span>
                            </td>
                            <td>{{ $delivery->delivery_time ? $delivery->delivery_time->format('d M Y, H:i') : '-' }}</td>
                            <td>{{ $delivery->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('deliveries.show', $delivery->delivery_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('deliveries.edit', $delivery->delivery_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteDeliveryModal-{{ $delivery->delivery_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteDeliveryModal-{{ $delivery->delivery_id }}" tabindex="-1">
                                   <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">Apakah Anda yakin ingin menghapus data pengiriman untuk Order #{{ $delivery->order_id }}?</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('deliveries.destroy', $delivery->delivery_id) }}" method="POST" style="display:inline;">@csrf @method('DELETE') <button type="submit" class="btn btn-danger">Hapus</button></form>
                                        </div>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pengiriman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($deliveries->hasPages())
            <div class="mt-3">
                {{ $deliveries->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection