@extends('layouts.app')

@section('title', 'Laporan Pergerakan Barang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up-arrow"></i> Laporan Pergerakan Barang</h2>
        <a href="{{ route('laporan.tidak_laku') }}" class="btn btn-warning">
            <i class="bi bi-exclamation-triangle"></i> Barang Tidak Laku
        </a>

    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.barang') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $namaBulan)
                                <option value="{{ $index + 1 }}" {{ $bulan == ($index + 1) ? 'selected' : '' }}>
                                    {{ $namaBulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cari Nama/Kode Barang</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Masukkan nama atau kode barang..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            @if($search)
                                <a href="{{ route('laporan.barang', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                                    class="btn btn-outline-secondary" title="Reset pencarian">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <!-- Info Pencarian -->
            @if($search)
                <div class="mt-3 alert alert-info alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Menampilkan hasil pencarian untuk: <strong>"{{ $search }}"</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Barang</h6>
                    <h3>{{ $stats['total_barang'] }}</h3>
                    <small>Item</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Barang Terjual</h6>
                    <h3>{{ $stats['barang_terjual'] }}</h3>
                    <small>Item bergerak</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Tidak Terjual</h6>
                    <h3>{{ $stats['barang_tidak_terjual'] }}</h3>
                    <small>Item stagnan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Total Nilai Keluar</h6>
                    <h4>Rp {{ number_format($stats['total_nilai_keluar'], 0, ',', '.') }}</h4>
                    <small>Penjualan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Pergerakan Barang -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Detail Pergerakan Barang -
                {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$bulan] }}
                {{ $tahun }}
                @if($search)
                    <span class="badge bg-info ms-2">Pencarian: {{ $search }}</span>
                @endif
            </h5>
            @if($search && $movements->count() > 0)
                <div class="text-muted">
                    <i class="bi bi-funnel"></i> {{ $movements->count() }} barang ditemukan
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Keluar</th>
                            <th class="text-end">Nilai Masuk</th>
                            <th class="text-end">Nilai Keluar</th>
                            <th class="text-center">Frekuensi</th>
                            <th class="text-center">Terakhir Keluar</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr class="{{ $movement->total_keluar == 0 ? 'table-warning' : '' }}">
                                <td>
                                    <strong>{{ $movement->barang->nama_barang }}</strong>
                                    <br><small class="text-muted">{{ $movement->barang->kode_barang }}</small>
                                </td>
                                <td class="text-center">
                                    {{ number_format($movement->total_masuk, 2) }} {{ $movement->barang->base_unit }}
                                </td>
                                <td class="text-center">
                                    <strong class="{{ $movement->total_keluar > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($movement->total_keluar, 2) }} {{ $movement->barang->base_unit }}
                                    </strong>
                                </td>
                                <td class="text-end">Rp {{ number_format($movement->nilai_masuk, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($movement->nilai_keluar, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <small>{{ $movement->frekuensi_masuk }} in / {{ $movement->frekuensi_keluar }} out</small>
                                </td>
                                <td class="text-center">
                                    @if($movement->last_keluar_date)
                                        {{ $movement->last_keluar_date->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $movement->last_keluar_date->diffForHumans() }}</small>
                                    @else
                                        <span class="badge bg-secondary">Belum pernah</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($movement->total_keluar > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Stagnan
                                        </span>
                                    @endif

                                    @if($movement->hari_tidak_terjual >= 90)
                                        <br><span class="badge bg-danger mt-1">{{ $movement->hari_tidak_terjual }} hari</span>
                                    @elseif($movement->hari_tidak_terjual >= 30)
                                        <br><span class="badge bg-warning mt-1">{{ $movement->hari_tidak_terjual }} hari</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    @if($search)
                                        <div class="py-3">
                                            <i class="bi bi-search display-6 text-muted"></i>
                                            <p class="mt-3">Tidak ada barang yang ditemukan untuk pencarian:
                                                <strong>"{{ $search }}"</strong></p>
                                            <a href="{{ route('laporan.barang', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                Tampilkan semua barang
                                            </a>
                                        </div>
                                    @else
                                        Tidak ada data untuk periode yang dipilih
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($search && $movements->count() > 0)
                <div class="mt-3 text-end">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Menampilkan {{ $movements->count() }} hasil pencarian untuk
                        "{{ $search }}"
                    </small>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .form-control:focus+.input-group-text {
            border-color: #86b7fe;
        }
    </style>
@endsection