@extends('layouts.admin')

@section('title', 'Buat Order Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Order Baru</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Order</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Pelanggan (Opsional)</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="order_type" class="form-label">Tipe Order <span class="text-danger">*</span></label>
                        <select class="form-select @error('order_type') is-invalid @enderror" id="order_type" name="order_type" required>
                            @foreach($orderTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('order_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('order_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status Order <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                             @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', App\Models\Order::STATUS_PENDING) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total_price" class="form-label">Total Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price" value="{{ old('total_price') }}" required min="0">
                        @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3" id="delivery_address_field" style="{{ old('order_type', array_key_first($orderTypes)) == App\Models\Order::TYPE_DELIVERY ? '' : 'display:none;' }}">
                    <label for="delivery_address" class="form-label">Alamat Pengiriman</label>
                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="delivery_address" name="delivery_address" rows="3">{{ old('delivery_address') }}</textarea>
                    @error('delivery_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('order_type').addEventListener('change', function() {
        var deliveryAddressField = document.getElementById('delivery_address_field');
        var deliveryAddressTextarea = document.getElementById('delivery_address');
        if (this.value === '{{ App\Models\Order::TYPE_DELIVERY }}') {
            deliveryAddressField.style.display = 'block';
            deliveryAddressTextarea.required = true;
        } else {
            deliveryAddressField.style.display = 'none';
            deliveryAddressTextarea.required = false;
        }
    });
    // Trigger change on page load if old value is delivery
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('order_type').value === '{{ App\Models\Order::TYPE_DELIVERY }}') {
            document.getElementById('delivery_address_field').style.display = 'block';
            document.getElementById('delivery_address').required = true;
        }
    });
</script>
@endpush
@endsection