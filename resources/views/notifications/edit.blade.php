@extends('layouts.app')

@section('title', 'Edit Notifikasi untuk ' . ($notification->user ? $notification->user->name : 'User'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Notifikasi (ID: {{ $notification->notif_id }})</h1>
        <a href="{{ route('notifications.index', ['user_id' => $notification->user_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Notifikasi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('notifications.update', $notification->notif_id) }}" method="POST">
                @csrf
                @method('PUT')

                <p><strong>Untuk User:</strong> {{ $notification->user ? $notification->user->name : 'N/A' }} (ID: {{ $notification->user_id }})</p>
                {{-- User ID tidak diubah --}}
                <input type="hidden" name="user_id" value="{{ $notification->user_id }}">


                <div class="mb-3">
                    <label for="message" class="form-label">Pesan Notifikasi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message', $notification->message) }}</textarea>
                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_read" name="is_read" value="1" {{ old('is_read', $notification->is_read) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_read">
                            Sudah Dibaca
                        </label>
                    </div>
                    @error('is_read') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-3">
                     <a href="{{ route('notifications.index', ['user_id' => $notification->user_id]) }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Notifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection