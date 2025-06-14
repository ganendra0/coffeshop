@extends('layouts.admin')

@section('title', 'Detail Item Order')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Item Order (ID: {{ $order_item->item_id }})</h1>
        <a href="{{ route('order_items.index', ['order_id' => $order_item->order_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar Item Order #{{ $order_item->order_id }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Item</h6>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Item ID:</dt>
                <dd class="col-sm-9">{{ $order_item->item_id }}</dd>

                <dt class="col-sm-3">Order ID:</dt>
                <dd class="col-sm-9"><a href="{{ route('orders.show', $order_item->order_id) }}">#{{ $order_item->order_id }}</a></dd>

                <dt class="col-sm-3">Menu:</dt>
                <dd class="col-sm-9">{{ $order_item->menu ? $order_item->menu->name : 'N/A' }}</dd>

                <dt class="col-sm-3">Jumlah:</dt>
                <dd class="col-sm-9">{{ $order_item->quantity }}</dd>

                @if($order_item->price_at_order) {{-- Jika Anda menambahkan kolom ini --}}
                <dt class="col-sm-3">Harga Saat Order:</dt>
                <dd class="col-sm-9">Rp {{ number_format($order_item->price_at_order, 0, ',', '.') }}</dd>
                @endif

                <dt class="col-sm-3">Catatan:</dt>
                <dd class="col-sm-9">{{ $order_item->notes ?: '-' }}</dd>

                <dt class="col-sm-3">Dibuat Pada:</dt>
                <dd class="col-sm-9">{{ $order_item->created_at->format('d M Y, H:i:s') }}</dd>

                <dt class="col-sm-3">Diperbarui Pada:</dt>
                <dd class="col-sm-9">{{ $order_item->updated_at->format('d M Y, H:i:s') }}</dd>
            </dl>
            <div class="mt-3">
                 <a href="{{ route('order_items.edit', $order_item->item_id) }}" class="btn btn-warning">Edit Item</a>
            </div>
        </div>
    </div>
</div>
@endsection