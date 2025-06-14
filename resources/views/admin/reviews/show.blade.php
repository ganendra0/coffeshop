@extends('layouts.admin')

@section('title', 'Detail Review Order #' . $review->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Review (ID: {{$review->review_id}})</h1>
        <div>
            <a href="{{ route('reviews.edit', $review->review_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit
            </a>
            <a href="{{ route('reviews.index', ['order_id' => $review->order_id]) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Review</h6>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Review ID:</dt>
                <dd class="col-sm-9">{{ $review->review_id }}</dd>

                <dt class="col-sm-3">Order ID:</dt>
                <dd class="col-sm-9"><a href="{{ $review->order ? route('orders.show', $review->order_id) : '#' }}">#{{ $review->order_id }}</a></dd>

                <dt class="col-sm-3">Pengguna:</dt>
                <dd class="col-sm-9">{{ $review->order && $review->order->user ? $review->order->user->name : ($review->user ? $review->user->name : 'N/A') }}</dd>

                <dt class="col-sm-3">Rating:</dt>
                <dd class="col-sm-9">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    ({{ $review->rating }}/5)
                </dd>

                <dt class="col-sm-3">Komentar:</dt>
                <dd class="col-sm-9">{{ $review->comment ?: '-' }}</dd>

                <dt class="col-sm-3">Tanggal Review:</dt>
                <dd class="col-sm-9">{{ $review->created_at ? $review->created_at->format('d M Y, H:i:s') : '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection