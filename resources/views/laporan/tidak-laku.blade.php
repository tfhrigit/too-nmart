@extends('layouts.app')

@section('title', 'Laporan Barang Tidak Laku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-exclamation-triangle text-warning"></i> Barang Tidak Laku</h2>
    <a href="{{ route('laporan.barang') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Pergerakan Barang
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.tidak_laku') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Barang dianggap tidak laku jika tidak terjual dalam:</label>
                    <div class="input-group">
                        <input type="number" name="bulan_batas" class="form-control" value="{{ $bulanBatas }}" min="1" max="12">
                        <span class="input-group-text">Bulan</span>
                    </div>
                    <small class="text-muted">Default: 3 bulan (90 hari)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alert Info -->
<div class="alert alert-warning">
    <i class="bi bi-info-circle"></i>
    <strong>Informasi:</strong> Menampilkan barang yang tidak terjual dalam <strong>{{ $bulanBatas }} bulan terakhir ({{ $bulanBatas * 30 }} hari)</strong> dan masih memiliki stok.
</div>

<!-- Summary Card -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h1 class="display-3">{{ $barangTidakLaku->count() }}</h1>
                <h5>Barang Tidak Laku</h5>
                <p class="mb-0">Perlu perhatian khusus</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Rekomendasi Aksi:</h6>
                <ul class="mb-0">
                    <li>Pertimbangkan untuk <strong>diskon</strong> atau promosi khusus</li>
                    <li>Evaluasi apakah barang masih dibutuhkan di stok</li>
                    <li>Periksa kondisi fisik barang (expired, rusak, dll)</li>
                    <li>Pertimbangkan untuk <strong>tukar guling</strong> atau <strong>retur</strong> ke supplier</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Table Barang Tidak Laku -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Daftar Barang Tidak Laku</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-danger">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Terakhir Terjual</th>
                        <th class="text-center">Hari Tidak Terjual</th>
                        <th class="text-center">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangTidakLaku as $index => $movement)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $movement->barang->kode_barang }}</code></td>
                            <td>
                                <strong>{{ $movement->barang->nama_barang }}</strong>
                                <br><small class="text-muted">{{ $movement->barang->deskripsi }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">
                                    {{ number_format($movement->barang->stok_sekarang, 2) }} {{ $movement->barang->base_unit }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($movement->last_keluar_date)
                                    {{ $movement->last_keluar_date->format('d M Y') }}
                                    <br><small class="text-muted">{{ $movement->last_keluar_date->diffForHumans() }}</small>
                                @else
                                    <span class="badge bg-secondary">Belum pernah terjual</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger fs-6">
                                    {{ $movement->hari_tidak_terjual }} hari
                                </span>
                                @if($movement->hari_tidak_terjual >= 180)
                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Sangat Kritis!</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($movement->hari_tidak_terjual >= 180)
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Sangat Tidak Laku</span>
                                @elseif($movement->hari_tidak_terjual >= 90)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Tidak Laku</span>
                                @else
                                    <span class="badge bg-info"><i class="bi bi-info-circle"></i> Perlu Perhatian</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('barang.show', $movement->barang_id) }}" class="btn btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('barang.edit', $movement->barang_id) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-emoji-smile fs-1 d-block mb-3 text-success"></i>
                                <h5>Tidak ada barang tidak laku!</h5>
                                <p>Semua barang bergerak dengan baik dalam {{ $bulanBatas }} bulan terakhir.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection