@extends('layouts.admin')

@section('title', $selectedOrderId ? 'Tambah Review Order #' . $selectedOrderId : 'Tambah Review Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $selectedOrderId ? 'Tambah Review untuk Order #' . $selectedOrderId : 'Tambah Review Baru' }}</h1>
        <a href="{{ route('reviews.index', $selectedOrderId ? ['order_id' => $selectedOrderId] : []) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Review</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="order_id" class="form-label">Order ID <span class="text-danger">*</span></label>
                    <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id" required {{ $selectedOrderId ? 'disabled' : '' }}>
                        <option value="">-- Pilih Order (Status Selesai & Belum Direview) --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->order_id }}" {{ old('order_id', $selectedOrderId) == $order->order_id ? 'selected' : '' }}>
                                #{{ $order->order_id }} - {{ $order->user ? $order->user->name : 'Guest' }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedOrderId)
                        <input type="hidden" name="order_id" value="{{ $selectedOrderId }}">
                    @endif
                    @error('order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                    <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating" required>
                        <option value="">-- Beri Rating --</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                    @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Komentar (Opsional)</label>
                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="4">{{ old('comment') }}</textarea>
                    @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection