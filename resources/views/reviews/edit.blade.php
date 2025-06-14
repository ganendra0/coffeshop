@extends('layouts.admin')

@section('title', 'Edit Review Order #' . $review->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Review untuk Order #{{ $review->order_id }}</h1>
        <a href="{{ route('reviews.index', ['order_id' => $review->order_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Review (ID: {{ $review->review_id }})</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reviews.update', $review->review_id) }}" method="POST">
                @csrf
                @method('PUT')

                <p><strong>Order ID:</strong> #{{ $review->order_id }} ({{ $review->order && $review->order->user ? $review->order->user->name : 'Guest' }})</p>
                {{-- Order ID tidak diubah --}}
                <input type="hidden" name="order_id" value="{{ $review->order_id }}">

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                    <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating" required>
                        <option value="">-- Beri Rating --</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                    @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Komentar (Opsional)</label>
                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="4">{{ old('comment', $review->comment) }}</textarea>
                    @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('reviews.index', ['order_id' => $review->order_id]) }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection