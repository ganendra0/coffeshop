@extends('layouts.admin')

@section('title', 'Tambah Item ke Order')

@php
    $formTitle = $selectedOrderId ? 'Tambah Item ke Order #' . $selectedOrderId : 'Tambah Item Order Baru';
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $formTitle }}</h1>
        <a href="{{ route('order_items.index', $selectedOrderId ? ['order_id' => $selectedOrderId] : []) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar Item
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Item Order</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('order_items.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="order_id" class="form-label">Order ID <span class="text-danger">*</span></label>
                    <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id" required {{ $selectedOrderId ? 'readonly' : '' }}>
                        <option value="">-- Pilih Order --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->order_id }}" {{ old('order_id', $selectedOrderId) == $order->order_id ? 'selected' : '' }}>
                                Order #{{ $order->order_id }} ({{ $order->user ? $order->user->name : 'Guest' }} - {{ $order->created_at->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    @if($selectedOrderId)
                        <input type="hidden" name="order_id" value="{{ $selectedOrderId }}">
                    @endif
                    @error('order_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                    <select class="form-select @error('menu_id') is-invalid @enderror" id="menu_id" name="menu_id" required>
                        <option value="">-- Pilih Menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->menu_id }}" data-price="{{ $menu->price }}" {{ old('menu_id') == $menu->menu_id ? 'selected' : '' }}>
                                {{ $menu->name }} (Rp {{ number_format($menu->price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('menu_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required min="1">
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jika Anda menambahkan price_at_order --}}
                {{-- <div class="mb-3">
                    <label for="price_at_order" class="form-label">Harga Saat Order (Rp) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('price_at_order') is-invalid @enderror" id="price_at_order" name="price_at_order" value="{{ old('price_at_order') }}" required readonly>
                    @error('price_at_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}


                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Jika Anda ingin mengisi price_at_order otomatis dari menu yang dipilih --}}
{{-- @push('scripts')
<script>
    document.getElementById('menu_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        document.getElementById('price_at_order').value = price ? parseFloat(price).toFixed(2) : '';
    });
    // Trigger on load if a menu is already selected (e.g., from old input)
    document.addEventListener('DOMContentLoaded', function() {
        var menuSelect = document.getElementById('menu_id');
        if (menuSelect.value) {
            var selectedOption = menuSelect.options[menuSelect.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            document.getElementById('price_at_order').value = price ? parseFloat(price).toFixed(2) : '';
        }
    });
</script>
@endpush --}}
@endsection