@extends('layouts.admin')

@section('title', 'Edit Pembayaran Order #' . $payment->order_id)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pembayaran Order #{{ $payment->order_id }}</h1>
        <a href="{{ route('payments.index', ['order_id' => $payment->order_id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Pembayaran (ID: {{ $payment->payment_id }})</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.update', $payment->payment_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <p><strong>Order ID:</strong> #{{ $payment->order_id }} ({{ $payment->order && $payment->order->user ? $payment->order->user->name : 'Guest' }})</p>
                {{-- Order ID tidak diubah --}}
                <input type="hidden" name="order_id" value="{{ $payment->order_id }}">


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Jumlah Pembayaran (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required min="0">
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $payment->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                 <div class="mb-3">
                    <label for="payment_time" class="form-label">Waktu Pembayaran (Opsional)</label>
                    <input type="datetime-local" class="form-control @error('payment_time') is-invalid @enderror" id="payment_time" name="payment_time" value="{{ old('payment_time', $payment->payment_time ? $payment->payment_time->format('Y-m-d\TH:i') : '') }}">
                    @error('payment_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="payment_proof_file" class="form-label">Bukti Pembayaran (Ganti jika perlu)</label>
                    <input type="file" class="form-control @error('payment_proof_file') is-invalid @enderror" id="payment_proof_file" name="payment_proof_file" onchange="previewPaymentProof()">
                    @error('payment_proof_file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    @if($payment->payment_proof_url)
                        <p class="mt-2">Bukti Saat Ini:</p>
                        <img id="proofPreview" src="{{ $payment->payment_proof_url }}" alt="Bukti Saat Ini" class="img-thumbnail" style="max-height: 200px;">
                    @else
                        <img id="proofPreview" src="#" alt="Preview Bukti" class="img-thumbnail mt-2" style="display:none; max-height: 200px;">
                    @endif
                </div>


                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('payments.index', ['order_id' => $payment->order_id]) }}" class="btn btn-outline-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function previewPaymentProof() {
        const image = document.querySelector('#payment_proof_file');
        const imgPreview = document.querySelector('#proofPreview');
         if (image.files && image.files[0]) {
            imgPreview.style.display = 'block';
            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);
            oFReader.onload = function(oFREvent) { imgPreview.src = oFREvent.target.result; }
        } else {
            // Jangan sembunyikan jika sudah ada gambar awal
            @if(!$payment->payment_proof_url)
                imgPreview.style.display = 'none';
                imgPreview.src = '#';
            @endif
        }
    }
</script>
@endpush
@endsection