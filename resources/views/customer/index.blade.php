@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header dengan tombol tambah -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-people"></i> Data Pelanggan
                    </h4>
                    <a href="{{ route('customer.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-person-plus"></i> Tambah Pelanggan
                    </a>
                </div>
                
                <!-- Search dan Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('customer.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Cari kode pelanggan, nama, atau nomor HP..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabel Data -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="60">No</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>No. HP</th>
                                    <th>Alamat</th>
                                    <th class="text-center" width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $key => $customer)
                                <tr>
                                    <td class="text-center">{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $customer->kode_customer }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $customer->nama_customer }}</strong>
                                    </td>
                                    <td>
                                        @if($customer->no_hp)
                                            <a href="tel:{{ $customer->no_hp }}" class="text-decoration-none">
                                                {{ $customer->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($customer->alamat)
                                            {{ Str::limit($customer->alamat, 30) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Detail Button -->
                                            <a href="{{ route('customer.show', $customer) }}" 
                                               class="btn btn-info" 
                                               title="Detail Pelanggan"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('customer.edit', $customer) }}" 
                                               class="btn btn-warning" 
                                               title="Edit Pelanggan"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <!-- Delete Button dengan Confirmation -->
                                            <form action="{{ route('customer.destroy', $customer) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ $customer->nama_customer }}?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" 
                                                        title="Hapus Pelanggan"
                                                        data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-person-x display-6"></i>
                                            <p class="mt-2 mb-0">Belum ada data pelanggan</p>
                                            @if(request('search'))
                                                <p class="small">Coba dengan kata kunci lain</p>
                                            @else
                                                <a href="{{ route('customer.create') }}" class="btn btn-success btn-sm mt-2">
                                                    <i class="bi bi-person-plus"></i> Tambah Pelanggan Pertama
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($customers->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} dari {{ $customers->total() }} pelanggan
                        </div>
                        <div>
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Success Message Toast -->
@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto"><i class="bi bi-check-circle"></i> Berhasil</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>

<script>
    // Auto hide toast setelah 5 detik
    setTimeout(() => {
        const toast = document.getElementById('successToast');
        if (toast) {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        }
    }, 5000);
</script>
@endif
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group-sm > .btn {
        border-radius: 4px !important;
    }
    .btn-group-sm > .btn:first-child {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .btn-group-sm > .btn:last-child {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@push('scripts')
<script>
    // Inisialisasi tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush