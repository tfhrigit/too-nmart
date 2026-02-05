@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box-arrow-up"></i> Barang Keluar</h2>
            <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Input Barang Keluar
            </a>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-control">
                            <option value="">Semua</option>
                            <option value="cash" {{ request('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="qris" {{ request('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="transfer" {{ request('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari no. transaksi/nama barang..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">
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
                @if($barangKeluars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Item</th>
                                    <th>Customer</th>
                                    <th>Metode Bayar</th>
                                    <th>Total Transaksi</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangKeluars as $transaksi)
                                    <tr>
                                        <td>
                                            <strong>{{ $transaksi->no_transaksi }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> {{ $transaksi->user->name ?? '-' }}
                                            </small>
                                        </td>
                                        <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $transaksi->items->count() }} item</span>
                                            <br>
                                            <small class="text-muted">
                                                @php
                                                    $itemNames = [];
                                                    foreach($transaksi->items as $item) {
                                                        $itemNames[] = $item->barang ? $item->barang->nama_barang : ($item->nama_barang_manual ?? 'Barang Manual');
                                                    }
                                                @endphp
                                                {{ implode(', ', array_slice($itemNames, 0, 2)) }}
                                                @if(count($itemNames) > 2)
                                                    ...
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($transaksi->customer)
                                                <strong>{{ $transaksi->customer->nama_customer }}</strong>
                                                @if($transaksi->customer->no_hp)
                                                    <br><small class="text-muted">Telp: {{ $transaksi->customer->no_hp }}</small>
                                                @endif
                                                @if($transaksi->customer->kode_customer)
                                                    <br><small class="text-muted">Kode: {{ $transaksi->customer->kode_customer }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Umum</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! $transaksi->metode_pembayaran_badge !!}
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($transaksi->total_transaksi, 0, ',', '.') }}</strong>
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
                                                <a href="{{ route('barang-keluar.show', $transaksi) }}" 
                                                   class="btn btn-info" title="Detail" data-bs-toggle="tooltip">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('barang-keluar.edit', $transaksi) }}" 
                                                   class="btn btn-warning" title="Edit" data-bs-toggle="tooltip">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <!-- Form Hapus -->
                                                <form action="{{ route('barang-keluar.destroy', $transaksi) }}" 
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

                    <div class="mt-3">
                        {{ $barangKeluars->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-arrow-up display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada data barang keluar</h4>
                        <p class="text-muted">Mulai dengan menambahkan transaksi barang keluar baru</p>
                        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Tambah Barang Keluar
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