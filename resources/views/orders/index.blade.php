@extends('layouts.admin')

@section('title', 'Manajemen Order')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Order</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Buat Order Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Order</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTableOrders" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Total (Rp)</th>
                            <th>Metode Bayar</th>
                            <th>Tgl Order</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->order_id }}</td>
                            <td>{{ $order->user ? $order->user->name : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $order->order_type == App\Models\Order::TYPE_DELIVERY ? 'info' : 'secondary' }}">
                                    {{ ucfirst($order->order_type) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    if ($order->status == App\Models\Order::STATUS_PENDING) $statusClass = 'warning text-dark';
                                    if ($order->status == App\Models\Order::STATUS_PROCESSING) $statusClass = 'primary';
                                    if ($order->status == App\Models\Order::STATUS_DELIVERING) $statusClass = 'info';
                                    if ($order->status == App\Models\Order::STATUS_COMPLETED) $statusClass = 'success';
                                    if ($order->status == App\Models\Order::STATUS_CANCELLED) $statusClass = 'danger';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order->order_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteOrderModal-{{ $order->order_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteOrderModal-{{ $order->order_id }}" tabindex="-1" aria-labelledby="deleteOrderModalLabel-{{ $order->order_id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteOrderModalLabel-{{ $order->order_id }}">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus order #{{ $order->order_id }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('orders.destroy', $order->order_id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection