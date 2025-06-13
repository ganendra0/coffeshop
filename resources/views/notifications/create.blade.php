@extends('layouts.app')

@section('title', $selectedUserId ? 'Kirim Notifikasi ke User ID ' . $selectedUserId : 'Kirim Notifikasi Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $selectedUserId ? 'Kirim Notifikasi ke User ID ' . $selectedUserId : 'Kirim Notifikasi Baru' }}</h1>
        <a href="{{ route('notifications.index', $selectedUserId ? ['user_id' => $selectedUserId] : []) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Notifikasi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('notifications.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Kirim ke User <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required {{ $selectedUserId ? 'disabled' : '' }}>
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ old('user_id', $selectedUserId) == $user->user_id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @if($selectedUserId)
                        <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
                    @endif
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Pesan Notifikasi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_read" name="is_read" value="1" {{ old('is_read') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_read">
                            Tandai sudah dibaca saat dikirim (Default: Belum Dibaca)
                        </label>
                    </div>
                    @error('is_read') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>


                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim Notifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection