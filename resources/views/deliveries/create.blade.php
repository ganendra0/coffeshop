@extends('layouts.app')

@section('title', $selectedOrderId ? 'Tugaskan Pengiriman Order #' . $selectedOrderId : 'Tugaskan Pengiriman Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $selectedOrderId ? 'Tugaskan Pengiriman untuk Order #' . $selectedOrderId : 'Tugaskan Pengiriman Baru' }}</h1>
        <a href="{{ route('deliveries.index', $selectedOrderId ? ['order_id' => $selectedOrderId] : []) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Pengiriman</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('deliveries.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="order_id" class="form-label">Order (Tipe Delivery & Belum Ada Pengiriman Aktif) <span class="text-danger">*</span></label>
                        <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id" required {{ $selectedOrderId ? 'disabled' : '' }}>
                            <option value="">-- Pilih Order --</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_id }}" {{ old('order_id', $selectedOrderId) == $order->order_id ? 'selected' : '' }}>
                                    #{{ $order->order_id }} - {{ $order->user ? $order->user->name : 'Guest' }}
                                    @if($order->delivery_address) (Alamat: {{ Str::limit($order->delivery_address, 30) }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @if($selectedOrderId)
                            <input type="hidden" name="order_id" value="{{ $selectedOrderId }}">
                        @endif
                        @error('order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="driver_id" class="form-label">Driver (Status Available) <span class="text-danger">*</span></label>
                        <select class="form-select @error('driver_id') is-invalid @enderror" id="driver_id" name="driver_id" required>
                            <option value="">-- Pilih Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->driver_id }}" {{ old('driver_id') == $driver->driver_id ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->vehicle_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Pengiriman <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', App\Models\Delivery::STATUS_ASSIGNED) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="delivery_time" class="form-label">Waktu Aktual Pengiriman (Isi jika sudah terkirim)</label>
                        <input type="datetime-local" class="form-control @error('delivery_time') is-invalid @enderror" id="delivery_time" name="delivery_time" value="{{ old('delivery_time') }}">
                        @error('delivery_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-truck-loading me-1"></i> Tugaskan & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection