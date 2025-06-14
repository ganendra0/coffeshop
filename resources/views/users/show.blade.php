@extends('layouts.admin')

@section('title', 'Detail Pengguna: ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4"> 
        <h1 class="h3 mb-0 text-gray-800">Detail Pengguna: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit Pengguna
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">ID Pengguna:</dt>
                        <dd class="col-sm-8">{{ $user->user_id }}</dd>

                        <dt class="col-sm-4">Nama Lengkap:</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4">Telepon:</dt>
                        <dd class="col-sm-8">{{ $user->phone ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Alamat:</dt>
                        <dd class="col-sm-8">{{ $user->address ?? '-' }}</dd>

                        <dt class="col-sm-4">Dibuat Pada:</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('d M Y, H:i:s') }}</dd>

                        <dt class="col-sm-4">Diperbarui Pada:</dt>
                        <dd class="col-sm-8">{{ $user->updated_at->format('d M Y, H:i:s') }}</dd>
                    </dl>
                </div>
            </div>
             {{-- Jangan tampilkan password atau remember_token di sini --}}
        </div>
    </div>
</div>
@endsection