@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box-arrow-in-down"></i> Barang Masuk</h2>
            <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Input Barang Masuk
            </a>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('barang-masuk.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" 
                               value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">Semua Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" 
                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }} ({{ $supplier->kode_supplier ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari no. transaksi/invoice/nama barang..." 
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                @if(session('no_transaksi'))
                    <br><small>No. Transaksi: {{ session('no_transaksi') }}</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($transaksis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Item</th>
                                    <th>Supplier</th>
                                    <th>Invoice Supplier</th>
                                    <th>Total Transaksi</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksis as $transaksi)
                                    <tr>
                                        <td>
                                            <strong>{{ $transaksi->no_transaksi }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> {{ $transaksi->user->name ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ $transaksi->tanggal->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $transaksi->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $transaksi->items->count() }} item</span>
                                            <br>
                                            <small class="text-muted">
                                                @php
                                                    $itemNames = [];
                                                    foreach($transaksi->items as $item) {
                                                        $itemNames[] = $item->barang ? $item->barang->nama_barang : 'Barang Manual';
                                                    }
                                                @endphp
                                                {{ implode(', ', array_slice($itemNames, 0, 2)) }}
                                                @if(count($itemNames) > 2)
                                                    ...
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($transaksi->supplier)
                                                <strong>{{ $transaksi->supplier->nama_supplier }}</strong>
                                                @if($transaksi->supplier->no_hp)
                                                    <br><small class="text-muted">Telp: {{ $transaksi->supplier->no_hp }}</small>
                                                @endif
                                                @if($transaksi->supplier->kode_supplier)
                                                    <br><small class="text-muted">Kode: {{ $transaksi->supplier->kode_supplier }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaksi->invoice_supplier)
                                                <span class="badge bg-secondary">{{ $transaksi->invoice_supplier }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</strong>
                                            @if($transaksi->items->count() > 0)
                                                <br>
                                                <small class="text-muted">
                                                    ~ Rp {{ number_format($transaksi->grand_total / $transaksi->items->count(), 0, ',', '.') }}/item
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaksi->keterangan)
                                                <span title="{{ $transaksi->keterangan }}">
                                                    {{ Str::limit($transaksi->keterangan, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Tombol Detail -->
                                                <a href="{{ route('barang-masuk.show', $transaksi->no_transaksi) }}" 
                                                   class="btn btn-info" title="Detail" data-bs-toggle="tooltip">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('barang-masuk.edit', $transaksi->no_transaksi) }}" 
                                                   class="btn btn-warning" title="Edit" data-bs-toggle="tooltip">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <!-- Form Hapus -->
                                                <form action="{{ route('barang-masuk.destroy', $transaksi->no_transaksi) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" 
                                                            title="Hapus" data-bs-toggle="tooltip">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $transaksis->firstItem() }} - {{ $transaksis->lastItem() }} dari
                            {{ $transaksis->total() }} transaksi
                        </div>
                        <div>
                            {{ $transaksis->withQueryString()->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada data barang masuk</h4>
                        <p class="text-muted">Mulai dengan menambahkan transaksi barang masuk baru</p>
                        <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Tambah Barang Masuk
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
    }
    [title] {
        cursor: help;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
    <script>
        // Inisialisasi tooltip
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush