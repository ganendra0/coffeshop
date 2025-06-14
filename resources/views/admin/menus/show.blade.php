@extends('layouts.admin')

@section('title', 'Detail Menu: ' . $menu->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Menu: {{ $menu->name }}</h1>
        <div>
            <a href="{{ route('menus.edit', $menu->menu_id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit Menu
            </a>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Menu</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if($menu->image_url)
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    @else
                        <div class="img-fluid rounded mb-3 bg-light d-flex align-items-center justify-content-center" style="height: 200px; width:100%;">
                            <span class="text-muted">Gambar Tidak Tersedia</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-3">ID Menu:</dt>
                        <dd class="col-sm-9">{{ $menu->menu_id }}</dd>

                        <dt class="col-sm-3">Nama Menu:</dt>
                        <dd class="col-sm-9">{{ $menu->name }}</dd>

                        <dt class="col-sm-3">Harga:</dt>
                        <dd class="col-sm-9">Rp {{ number_format($menu->price, 0, ',', '.') }}</dd>

                        <dt class="col-sm-3">Kategori:</dt>
                        <dd class="col-sm-9">{{ $menu->category }}</dd>

                        <dt class="col-sm-3">Stok:</dt>
                        <dd class="col-sm-9">{{ $menu->stock }}</dd>

                        <dt class="col-sm-3">Ketersediaan:</dt>
                        <dd class="col-sm-9">
                            @if($menu->is_available)
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Habis</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $menu->created_at ? $menu->created_at->format('d M Y, H:i:s') : '-' }}</dd>

                        <dt class="col-sm-3">Diperbarui Pada:</dt>
                        <dd class="col-sm-9">{{ $menu->updated_at ? $menu->updated_at->format('d M Y, H:i:s') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection