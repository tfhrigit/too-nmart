@extends('layouts.app')

@section('title', 'Detail Pergerakan Barang')

@section('content')
<div class="mb-4">
    <a href="{{ route('barang-movement.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-arrow-left-right"></i> Detail Pergerakan Barang
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Informasi Barang</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Kode Barang</strong></td>
                        <td>: {{ $movement->barang->kode_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama Barang</strong></td>
                        <td>: {{ $movement->barang->nama_barang ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Periode</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Tahun</strong></td>
                        <td>: {{ $movement->tahun }}</td>
                    </tr>
                    <tr>
                        <td><strong>Bulan</strong></td>
                        <td>: {{ $movement->bulan_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3"><i class="bi bi-box-arrow-in-down text-success"></i> Barang Masuk</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="50%"><strong>Total Kuantitas</strong></td>
                        <td>: <span class="badge bg-success">{{ number_format($movement->total_masuk, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Total Nilai</strong></td>
                        <td>: <strong>Rp {{ number_format($movement->nilai_masuk, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Frekuensi</strong></td>
                        <td>: {{ $movement->frekuensi_masuk }} kali</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="text-muted mb-3"><i class="bi bi-box-arrow-up text-danger"></i> Barang Keluar</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="50%"><strong>Total Kuantitas</strong></td>
                        <td>: <span class="badge bg-danger">{{ number_format($movement->total_keluar, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Total Nilai</strong></td>
                        <td>: <strong>Rp {{ number_format($movement->nilai_keluar, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Frekuensi</strong></td>
                        <td>: {{ $movement->frekuensi_keluar }} kali</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($movement->last_keluar_date)
        <hr>
        <div class="alert alert-info">
            <strong>Tanggal Keluar Terakhir:</strong> {{ $movement->last_keluar_date->format('d/m/Y') }}
            @if($movement->hari_tidak_terjual > 0)
            <br><strong>Hari Tidak Terjual:</strong> {{ $movement->hari_tidak_terjual }} hari
            @endif
        </div>
        @endif
    </div>
</div>

@endsection
