@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header dengan tombol tambah -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-truck"></i> Data Supplier
                    </h4>
                    <a href="{{ route('supplier.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Supplier
                    </a>
                </div>
                
                <!-- Search dan Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('supplier.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Cari kode supplier, nama, atau nomor HP..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
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
                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>No. HP</th>
                                    <th>Email</th>
                                    <th class="text-center" width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $key => $supplier)
                                <tr>
                                    <td class="text-center">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $supplier->kode_supplier }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $supplier->nama_supplier }}</strong>
                                    </td>
                                    <td>
                                        @if($supplier->alamat)
                                            {{ Str::limit($supplier->alamat, 30) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->no_hp)
                                            {{ $supplier->no_hp }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                                {{ $supplier->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Detail Button -->
                                            <a href="{{ route('supplier.show', $supplier) }}" 
                                               class="btn btn-info" 
                                               title="Detail Supplier" 
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('supplier.edit', $supplier) }}" 
                                               class="btn btn-warning" 
                                               title="Edit Supplier"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <!-- Delete Button dengan Confirmation -->
                                            <form action="{{ route('supplier.destroy', $supplier) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier {{ $supplier->nama_supplier }}?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus Supplier" data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-people display-6"></i>
                                            <p class="mt-2 mb-0">Belum ada data supplier</p>
                                            @if(request('search'))
                                                <p class="small">Coba dengan kata kunci lain</p>
                                            @else
                                                <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm mt-2">
                                                    <i class="bi bi-plus-circle"></i> Tambah Supplier Pertama
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
                @if($suppliers->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} supplier
                        </div>
                        <div>
                            {{ $suppliers->withQueryString()->links() }}
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