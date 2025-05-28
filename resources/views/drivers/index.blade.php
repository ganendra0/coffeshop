@extends('layouts.app')

@section('title', 'Manajemen Driver')

@php
    $currentStatus = request()->get('status');
    $currentVehicleType = request()->get('vehicle_type');
    $pageTitle = 'Daftar Driver';
    if ($currentStatus) $pageTitle .= ' (Status: ' . ($statuses[$currentStatus] ?? ucfirst($currentStatus)) . ')';
    if ($currentVehicleType) $pageTitle .= ' (Kendaraan: ' . ($vehicleTypes[$currentVehicleType] ?? ucfirst($currentVehicleType)) . ')';
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route('drivers.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tambah Driver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <form action="{{ route('drivers.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <h6 class="m-0 font-weight-bold text-primary">Data Driver</h6>
                </div>
                <div class="col-md-3">
                     <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $currentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="col-md-3">
                     <select name="vehicle_type" class="form-select form-select-sm">
                        <option value="">Semua Tipe Kendaraan</option>
                        @foreach($vehicleTypes as $value => $label)
                            <option value="{{ $value }}" {{ $currentVehicleType == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
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
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Tipe Kendaraan</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                        <tr>
                            <td>{{ $driver->driver_id }}</td>
                            <td>{{ $driver->name }}</td>
                            <td>{{ $driver->phone }}</td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    if ($driver->status == App\Models\Driver::STATUS_AVAILABLE) $statusClass = 'success';
                                    if ($driver->status == App\Models\Driver::STATUS_ON_DELIVERY) $statusClass = 'info';
                                    if ($driver->status == App\Models\Driver::STATUS_UNAVAILABLE) $statusClass = 'danger';
                                @endphp
                                <span class="badge bg-{{$statusClass}}">{{ $statuses[$driver->status] ?? ucfirst($driver->status) }}</span>
                            </td>
                            <td>{{ $vehicleTypes[$driver->vehicle_type] ?? ucfirst($driver->vehicle_type) }}</td>
                            <td>{{ $driver->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('drivers.show', $driver->driver_id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('drivers.edit', $driver->driver_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteDriverModal-{{ $driver->driver_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteDriverModal-{{ $driver->driver_id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">Apakah Anda yakin ingin menghapus driver <strong>{{$driver->name}}</strong>?</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('drivers.destroy', $driver->driver_id) }}" method="POST" style="display:inline;">@csrf @method('DELETE') <button type="submit" class="btn btn-danger">Hapus</button></form>
                                        </div>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data driver.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($drivers->hasPages())
            <div class="mt-3">
                {{ $drivers->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection