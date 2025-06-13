@extends('layouts.app')

@section('title', 'Edit Pengiriman Order #' . $delivery->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengiriman Order #{{ $delivery->order_id }}</h1>
        <a href="{{ route('deliveries.index', ['order_id' => $delivery->order_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Pengiriman (ID: {{$delivery->delivery_id}})</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('deliveries.update', $delivery->delivery_id) }}" method="POST">
                @csrf
                @method('PUT')

                <p><strong>Order ID:</strong> <a href="{{ route('orders.show', $delivery->order_id) }}">#{{ $delivery->order_id }}</a>
                   ({{ $delivery->order && $delivery->order->user ? $delivery->order->user->name : 'Guest' }})
                </p>
                <input type="hidden" name="order_id" value="{{ $delivery->order_id }}">


                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                        <select class="form-select @error('driver_id') is-invalid @enderror" id="driver_id" name="driver_id" required>
                            <option value="">-- Pilih Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->driver_id }}" {{ old('driver_id', $delivery->driver_id) == $driver->driver_id ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->vehicle_type }}) - Status: {{ $driver->status }}
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Pengiriman <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $delivery->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 <div class="mb-3">
                    <label for="delivery_time" class="form-label">Waktu Aktual Pengiriman (Isi jika sudah/saat terkirim)</label>
                    <input type="datetime-local" class="form-control @error('delivery_time') is-invalid @enderror" id="delivery_time" name="delivery_time" value="{{ old('delivery_time', $delivery->delivery_time ? $delivery->delivery_time->format('Y-m-d\TH:i') : '') }}">
                    @error('delivery_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                <div class="d-flex justify-content-end mt-3">
                     <a href="{{ route('deliveries.index', ['order_id' => $delivery->order_id]) }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Pengiriman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection