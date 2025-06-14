@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Order #{{ $order->order_id }}</h1>
        <div>
            <a href="{{ route('orders.edit', $order->order_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit Order
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Order</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">ID Order:</dt>
                        <dd class="col-sm-8">#{{ $order->order_id }}</dd>

                        <dt class="col-sm-4">Pelanggan:</dt>
                        <dd class="col-sm-8">{{ $order->user ? $order->user->name . ' (' . $order->user->email . ')' : 'N/A (Guest)' }}</dd>

                        <dt class="col-sm-4">Tipe Order:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $order->order_type == App\Models\Order::TYPE_DELIVERY ? 'info' : 'secondary' }}">
                                {{ ucfirst($order->order_type) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
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
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Total Harga:</dt>
                        <dd class="col-sm-8">Rp {{ number_format($order->total_price, 0, ',', '.') }}</dd>

                        <dt class="col-sm-4">Metode Bayar:</dt>
                        <dd class="col-sm-8">{{ $order->payment_method }}</dd>

                        @if($order->order_type == App\Models\Order::TYPE_DELIVERY && $order->delivery_address)
                        <dt class="col-sm-4">Alamat Kirim:</dt>
                        <dd class="col-sm-8">{{ $order->delivery_address }}</dd>
                        @endif

                        <dt class="col-sm-4">Tgl Order:</dt>
                        <dd class="col-sm-8">{{ $order->created_at->format('d M Y, H:i:s') }}</dd>

                        <dt class="col-sm-4">Diperbarui:</dt>
                        <dd class="col-sm-8">{{ $order->updated_at->format('d M Y, H:i:s') }}</dd>
                    </dl>
                </div>
            </div>

            <hr>
            <h5>Item Pesanan:</h5>
            @if($order->items && $order->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->menu ? $item->menu->name : 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->price_at_order, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada item untuk order ini (Fitur Item Order belum diimplementasikan sepenuhnya).</p>
            @endif

        </div>
    </div>
</div>
@endsection