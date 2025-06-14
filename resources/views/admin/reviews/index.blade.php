@extends('layouts.admin')

@section('title', 'Daftar Review')

@php
    $currentOrderId = request()->get('order_id');
    $currentRating = request()->get('rating');
    $pageTitle = 'Daftar Review';
    if ($currentOrderId) $pageTitle .= ' untuk Order #' . $currentOrderId;
    if ($currentRating) $pageTitle .= ' (Rating: ' . $currentRating . ' Bintang)';
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('reviews.create', $currentOrderId ? ['order_id' => $currentOrderId] : []) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tambah Review
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <form action="{{ route('reviews.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">Data Review</h6>
                </div>
                <div class="col-md-3">
                    <select name="order_id" class="form-select form-select-sm">
                        <option value="">Semua Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->order_id }}" {{ $currentOrderId == $order->order_id ? 'selected' : '' }}>
                                Order #{{ $order->order_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                     <select name="rating" class="form-select form-select-sm">
                        <option value="">Semua Rating</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $currentRating == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Pengguna</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->review_id }}</td>
                            <td>
                                <a href="{{ $review->order ? route('orders.show', $review->order_id) : '#' }}">#{{ $review->order_id }}</a>
                            </td>
                            <td>{{ $review->order && $review->order->user ? $review->order->user->name : 'N/A' }}</td>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                ({{ $review->rating }})
                            </td>
                            <td>{{ Str::limit($review->comment, 70) }}</td>
                            <td>{{ $review->created_at ? $review->created_at->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('reviews.show', $review->review_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('reviews.edit', $review->review_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteReviewModal-{{ $review->review_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteReviewModal-{{ $review->review_id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">Apakah Anda yakin ingin menghapus review untuk Order #{{ $review->order_id }}?</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('reviews.destroy', $review->review_id) }}" method="POST" style="display:inline;">@csrf @method('DELETE') <button type="submit" class="btn btn-danger">Hapus</button></form>
                                        </div>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data review.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reviews->hasPages())
            <div class="mt-3">
                {{ $reviews->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection