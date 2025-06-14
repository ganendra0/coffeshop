@extends('layouts.admin')

@section('title', 'Buat Order Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Order Baru</h1>
        {{-- Menggunakan nama route tanpa prefix admin agar cocok dengan route resource standar --}}
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
                        <label for="user_id" class="form-label">Pelanggan</label>
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
                        <label for="status" class="form-label">Status Pesanan <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                             @foreach($statuses as $value => $label)
                                {{-- Menggunakan konstanta untuk status default --}}
                                <option value="{{ $value }}" {{ old('status', \App\Models\Order::STATUS_PENDING) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="total_price" class="form-label">Total Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price" value="{{ old('total_price', 0) }}" required min="0">
                        @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Kolom delivery address dan notes jika masih dibutuhkan --}}
                <div class="mb-3">
                    <label for="notes_for_restaurant" class="form-label">Catatan untuk Restoran</label>
                    <textarea name="notes_for_restaurant" id="notes_for_restaurant" class="form-control @error('notes_for_restaurant') is-invalid @enderror" rows="3">{{ old('notes_for_restaurant') }}</textarea>
                    @error('notes_for_restaurant')
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
@endsection