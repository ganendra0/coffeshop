@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Menu Baru</h1>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Menu</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                        {{-- Jika kategori dinamis dari controller, gunakan select --}}
                        {{-- <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select> --}}
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="Contoh: Makanan, Minuman" required>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required min="0">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Gambar Menu</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" onchange="previewImage()">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <img id="imagePreview" src="#" alt="Preview Gambar" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                    </div>
                    <div class="col-md-6 mb-3 mt-md-4 pt-md-2">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', 1) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">
                                Tersedia (Centang jika menu tersedia)
                            </label>
                        </div>
                        @error('is_available')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('#imagePreview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
@endpush
@endsection