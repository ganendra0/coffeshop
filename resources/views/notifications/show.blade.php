@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Notifikasi (ID: {{$notification->notif_id}})</h1>
        <div>
            <a href="{{ route('notifications.edit', $notification->notif_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit
            </a>
            <a href="{{ route('notifications.index', ['user_id' => $notification->user_id]) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Notifikasi</h6>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Notif ID:</dt>
                <dd class="col-sm-9">{{ $notification->notif_id }}</dd>

                <dt class="col-sm-3">Untuk User:</dt>
                <dd class="col-sm-9">{{ $notification->user ? $notification->user->name . ' (ID: ' . $notification->user_id . ')' : 'N/A' }}</dd>

                <dt class="col-sm-3">Status Baca:</dt>
                <dd class="col-sm-9">
                     @if($notification->is_read)
                        <span class="badge bg-success">Sudah Dibaca</span>
                    @else
                        <span class="badge bg-danger">Belum Dibaca</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Pesan:</dt>
                <dd class="col-sm-9"><pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $notification->message }}</pre></dd>

                <dt class="col-sm-3">Tanggal Kirim:</dt>
                <dd class="col-sm-9">{{ $notification->created_at ? $notification->created_at->format('d M Y, H:i:s') : '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection