@extends('layouts.app')

@section('title', 'Daftar Notifikasi')

@php
    $currentUserId = request()->get('user_id');
    $currentIsRead = request()->get('is_read'); // Akan jadi '1' atau '0' atau ''
    $pageTitle = 'Daftar Notifikasi';
    if ($currentUserId) {
        $user = \App\Models\User::find($currentUserId); // Ambil user untuk nama
        $pageTitle .= ' untuk ' . ($user ? $user->name : 'User ID ' . $currentUserId);
    }
    if ($currentIsRead !== null && $currentIsRead !== '') {
        $pageTitle .= ' (Status: ' . ($currentIsRead === '1' ? 'Sudah Dibaca' : 'Belum Dibaca') . ')';
    }
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('notifications.create', $currentUserId ? ['user_id' => $currentUserId] : []) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-paper-plane fa-sm text-white-50 me-1"></i> Kirim Notifikasi
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <form action="{{ route('notifications.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">Data Notifikasi</h6>
                </div>
                <div class="col-md-3">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ $currentUserId == $user->user_id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                     <select name="is_read" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="1" {{ $currentIsRead === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
                        <option value="0" {{ $currentIsRead === '0' ? 'selected' : '' }}>Belum Dibaca</option>
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
                            <th>Untuk User</th>
                            <th>Pesan</th>
                            <th>Status Baca</th>
                            <th>Tanggal Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                        <tr class="{{ !$notification->is_read ? 'fw-bold table-warning' : '' }}">
                            <td>{{ $notification->notif_id }}</td>
                            <td>{{ $notification->user ? $notification->user->name : 'N/A' }}</td>
                            <td>{{ Str::limit($notification->message, 100) }}</td>
                            <td>
                                @if($notification->is_read)
                                    <span class="badge bg-success">Sudah Dibaca</span>
                                @else
                                    <span class="badge bg-danger">Belum Dibaca</span>
                                @endif
                            </td>
                            <td>{{ $notification->created_at ? $notification->created_at->format('d M Y, H:i') : '-'}}</td>
                            <td>
                                <a href="{{ route('notifications.show', $notification->notif_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('notifications.edit', $notification->notif_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Jika ada aksi markAsRead --}}
                                {{-- @if(!$notification->is_read)
                                <form action="{{ route('notifications.markAsRead', $notification->notif_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Tandai Sudah Dibaca"><i class="fas fa-check"></i></button>
                                </form>
                                @endif --}}
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteNotifModal-{{ $notification->notif_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteNotifModal-{{ $notification->notif_id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">Apakah Anda yakin ingin menghapus notifikasi ini?</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('notifications.destroy', $notification->notif_id) }}" method="POST" style="display:inline;">@csrf @method('DELETE') <button type="submit" class="btn btn-danger">Hapus</button></form>
                                        </div>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data notifikasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($notifications->hasPages())
            <div class="mt-3">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection