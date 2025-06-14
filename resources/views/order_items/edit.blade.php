@extends('layouts.admin')

@section('title', 'Edit Item Order #' . $order_item->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Item untuk Order #{{ $order_item->order_id }}</h1>
        <a href="{{ route('order_items.index', ['order_id' => $order_item->order_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali ke Daftar Item
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Item Order (Item ID: {{ $order_item->item_id }})</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('order_items.update', $order_item->item_id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Order ID biasanya tidak diubah untuk item yang sudah ada --}}
                <input type="hidden" name="order_id" value="{{ $order_item->order_id }}">
                <p><strong>Order ID:</strong> #{{ $order_item->order_id }}</p>


                <div class="mb-3">
                    <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                    <select class="form-select @error('menu_id') is-invalid @enderror" id="menu_id" name="menu_id" required>
                        <option value="">-- Pilih Menu --</option>
                        @foreach($menus as $menu)
                             <option value="{{ $menu->menu_id }}" data-price="{{ $menu->price }}" {{ old('menu_id', $order_item->menu_id) == $menu->menu_id ? 'selected' : '' }}>
                                {{ $menu->name }} (Rp {{ number_format($menu->price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('menu_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $order_item->quantity) }}" required min="1">
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $order_item->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jika Anda menambahkan price_at_order --}}
                {{-- <div class="mb-3">
                    <label for="price_at_order" class="form-label">Harga Saat Order (Rp) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('price_at_order') is-invalid @enderror" id="price_at_order" name="price_at_order" value="{{ old('price_at_order', $order_item->price_at_order) }}" required readonly>
                    @error('price_at_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="d-flex justify-content-end mt-3">
                     <a href="{{ route('order_items.index', ['order_id' => $order_item->order_id]) }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Jika Anda ingin mengisi price_at_order otomatis dari menu yang dipilih --}}
{{-- @push('scripts')
<script>
    document.getElementById('menu_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        document.getElementById('price_at_order').value = price ? parseFloat(price).toFixed(2) : '';
    });
     // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        var menuSelect = document.getElementById('menu_id');
        if (menuSelect.value) {
            var selectedOption = menuSelect.options[menuSelect.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            var priceField = document.getElementById('price_at_order');
            // Hanya isi jika price_at_order kosong (misal, baru pertama kali edit item lama yg belum ada price_at_order)
            // atau jika Anda memang ingin selalu update saat menu diubah
            if (!priceField.value || priceField.value === '0.00') { // Cek jika kosong atau 0
                 priceField.value = price ? parseFloat(price).toFixed(2) : '';
            }
        }
    });
</script>
@endpush --}}
@endsection