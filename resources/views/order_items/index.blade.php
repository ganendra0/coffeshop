@extends('layouts.admin')

@section('title', 'Daftar Item Order')

@php
    $currentOrderId = request()->get('order_id');
    $pageTitle = $currentOrderId ? 'Item untuk Order #' . $currentOrderId : 'Semua Item Order';
@endphp


@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('order_items.create', $currentOrderId ? ['order_id' => $currentOrderId] : []) }}" class="btn btn-primary shadow-sm">
    <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tambah Item
</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">Data Item Order</h6>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('order_items.index') }}" method="GET" class="d-flex justify-content-end">
                        <select name="order_id" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
                            <option value="">Semua Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_id }}" {{ $currentOrderId == $order->order_id ? 'selected' : '' }}>
                                    Order #{{ $order->order_id }} ({{ $order->user ? $order->user->name : 'Guest' }})
                                </option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="btn btn-sm btn-secondary">Filter</button></noscript>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Item</th>
                            <th>Order ID</th>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Tgl Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderItems as $item)
                        <tr>
                            <td>{{ $item->item_id }}</td>
                            <td>
                                <a href="{{ route('orders.show', $item->order_id) }}">#{{ $item->order_id }}</a>
                            </td>
                            <td>{{ $item->menu ? $item->menu->name : 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ Str::limit($item->notes, 50) }}</td>
                            <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('order_items.edit', $item->item_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteItemModal-{{ $item->item_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteItemModal-{{ $item->item_id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus item ini dari order #{{ $item->order_id }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('order_items.destroy', $item->item_id) }}" method="POST" style="display: inline;">
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
                            <td colspan="7" class="text-center">Tidak ada item untuk order ini atau tidak ada item order yang cocok dengan filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orderItems->hasPages())
            <div class="mt-3">
                {{ $orderItems->appends(request()->query())->links() }} {{-- penting untuk menjaga parameter filter saat paginasi --}}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection