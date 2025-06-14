@extends('layouts.admin')

@section('title', 'Manajemen Menu')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Menu</h1>
        <a href="{{ route('menus.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tambah Menu Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Menu</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTableMenus" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga (Rp)</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Ketersediaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $index => $menu)
                        <tr>
                            <td>{{ $index + 1 + ($menus->currentPage() - 1) * $menus->perPage() }}</td>
                            <td>{{ $menu->menu_id }}</td>
                            <td>
                                @if($menu->full_image_url)
                                    <img src="{{ $menu->full_image_url }}" alt="{{ $menu->name }}" width="80">
                                @else
                                    {{-- Tampilkan placeholder atau teks jika tidak ada gambar --}}
                                    {{-- <img src="{{ asset('images/placeholder_menu.png') }}" alt="No Image" width="80" class="img-thumbnail"> --}}
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $menu->name }}</td>
                            <td>{{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>{{ $menu->category }}</td>
                            <td>{{ $menu->stock }}</td>
                            <td>
                                @if($menu->is_available)
                                    <span class="badge bg-success">Tersedia</span>
                                @else
                                    <span class="badge bg-danger">Habis</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('menus.show', $menu->menu_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('menus.edit', $menu->menu_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteMenuModal-{{ $menu->menu_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Konfirmasi Hapus (sudah ada di kode Anda sebelumnya) -->
                                <div class="modal fade" id="deleteMenuModal-{{ $menu->menu_id }}" tabindex="-1" aria-labelledby="deleteMenuModalLabel-{{ $menu->menu_id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteMenuModalLabel-{{ $menu->menu_id }}">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus menu <strong>{{ $menu->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('menus.destroy', $menu->menu_id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data menu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($menus->hasPages())
            <div class="mt-3">
                {{ $menus->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection