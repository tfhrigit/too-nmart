@extends('layouts.app')

@section('title', 'Pergerakan Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-arrow-left-right"></i> Pergerakan Barang</h2>
        <p class="text-muted mb-0">Laporan bulanan pergerakan stok barang</p>
    </div>
    <a href="{{ route('barang-movement.riwayat-stok') }}" class="btn btn-primary">
        <i class="bi bi-table"></i> Riwayat Stok Harian
    </a>
</div>

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('barang-movement.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Barang</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nama atau kode barang..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach($months as $num => $month)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Pergerakan Barang</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Tahun</th>
                        <th>Bulan</th>
                        <th>Total Masuk</th>
                        <th>Total Keluar</th>
                        <th>Nilai Masuk</th>
                        <th>Nilai Keluar</th>
                        <th>Frekuensi Masuk</th>
                        <th>Frekuensi Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ ($movements->currentPage() - 1) * 15 + $loop->iteration }}</td>
                        <td><strong>{{ $movement->barang->kode_barang ?? '-' }}</strong></td>
                        <td>{{ $movement->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $movement->tahun }}</td>
                        <td>{{ $movement->bulan_name }}</td>
                        <td>
                            <span class="badge bg-success">
                                {{ number_format($movement->total_masuk, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger">
                                {{ number_format($movement->total_keluar, 2) }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($movement->nilai_masuk, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($movement->nilai_keluar, 0, ',', '.') }}</td>
                        <td>{{ $movement->frekuensi_masuk }}</td>
                        <td>{{ $movement->frekuensi_keluar }}</td>
                        <td>
                            <a href="{{ route('barang-movement.show', $movement) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> Tidak ada data pergerakan barang
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($movements->hasPages())
        <div class="mt-4">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
