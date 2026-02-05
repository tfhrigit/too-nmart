@extends('layouts.app')

@section('title', 'Laporan Detail Barang Masuk')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Laporan Detail Barang Masuk</h2>
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.barang-masuk') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        id="tanggal_mulai" 
                        name="tanggal_mulai" 
                        value="{{ $tanggalMulai }}"
                    >
                </div>
                <div class="col-md-5">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input 
                        type="date" 
                        class="form-control" 
                        id="tanggal_akhir" 
                        name="tanggal_akhir" 
                        value="{{ $tanggalAkhir }}"
                    >
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Pembelian</h6>
                    <h3>Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Jumlah Transaksi</h6>
                    <h3>{{ $summary['jumlah_transaksi'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Item</h6>
                    <h3>{{ $summary['total_item'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Barang Masuk</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $transaksi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $transaksi->no_transaksi }}</span>
                        </td>
                        <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $transaksi->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $transaksi->items->count() }}</td>
                        <td>
                            <span class="text-success fw-bold">
                                Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ $transaksi->user->name ?? '-' }}</td>
                        <td>
                            <a 
                                href="{{ route('barang-masuk.show', $transaksi->no_transaksi) }}" 
                                class="btn btn-sm btn-info"
                                title="Lihat Detail"
                            >
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            <i class="fas fa-inbox"></i> Tidak ada data barang masuk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection
