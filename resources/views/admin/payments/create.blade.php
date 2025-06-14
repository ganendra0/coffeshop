@extends('layouts.admin')

@section('title', $selectedOrderId ? 'Catat Pembayaran Order #' . $selectedOrderId : 'Catat Pembayaran Baru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $selectedOrderId ? 'Catat Pembayaran Order #' . $selectedOrderId : 'Catat Pembayaran Baru' }}</h1>
        <a href="{{ route('payments.index', $selectedOrderId ? ['order_id' => $selectedOrderId] : []) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="order_id" class="form-label">Order ID <span class="text-danger">*</span></label>
                        <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id" required {{ $selectedOrderId ? 'readonly disabled' : '' }}>
                            <option value="">-- Pilih Order --</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_id }}" data-amount="{{ $order->total_price }}" {{ old('order_id', $selectedOrderId) == $order->order_id ? 'selected' : '' }}>
                                    #{{ $order->order_id }} - {{ $order->user ? $order->user->name : 'Guest' }} (Total: Rp {{ number_format($order->total_price,0,',','.') }})
                                </option>
                            @endforeach
                        </select>
                        @if($selectedOrderId) {{-- Kirimkan order_id jika readonly --}}
                            <input type="hidden" name="order_id" value="{{ $selectedOrderId }}">
                        @endif
                        @error('order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Jumlah Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required min="0">
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', App\Models\Payment::STATUS_PENDING) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="payment_time" class="form-label">Waktu Pembayaran (Opsional)</label>
                        <input type="datetime-local" class="form-control @error('payment_time') is-invalid @enderror" id="payment_time" name="payment_time" value="{{ old('payment_time') }}">
                        @error('payment_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="payment_proof_file" class="form-label">Bukti Pembayaran (Opsional)</label>
                    <input type="file" class="form-control @error('payment_proof_file') is-invalid @enderror" id="payment_proof_file" name="payment_proof_file" onchange="previewPaymentProof()">
                    @error('payment_proof_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <img id="proofPreview" src="#" alt="Preview Bukti" class="img-thumbnail mt-2" style="display: none; max-height: 200px;">
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('order_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var amount = selectedOption.getAttribute('data-amount');
        document.getElementById('amount').value = amount ? parseFloat(amount).toFixed(2) : '';
    });
     // Trigger on load if an order is already selected (e.g., from old input or query param)
    document.addEventListener('DOMContentLoaded', function() {
        var orderSelect = document.getElementById('order_id');
        if (orderSelect.value && !document.getElementById('amount').value) { // Isi amount jika kosong
            var selectedOption = orderSelect.options[orderSelect.selectedIndex];
            var amount = selectedOption.getAttribute('data-amount');
            document.getElementById('amount').value = amount ? parseFloat(amount).toFixed(2) : '';
        }
    });

    function previewPaymentProof() {
        const image = document.querySelector('#payment_proof_file');
        const imgPreview = document.querySelector('#proofPreview');
        if (image.files && image.files[0]) {
            imgPreview.style.display = 'block';
            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);
            oFReader.onload = function(oFREvent) { imgPreview.src = oFREvent.target.result; }
        } else {
            imgPreview.style.display = 'none';
            imgPreview.src = '#';
        }
    }
</script>
@endpush
@endsection