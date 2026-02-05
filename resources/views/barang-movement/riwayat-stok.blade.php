@extends('layouts.app')

@section('title', 'Riwayat Stok')

@section('content')
<style>
    .riwayat-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }

    .periode-selector {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .periode-selector button {
        padding: 8px 16px;
        border: 2px solid #dee2e6;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #666;
    }

    .periode-selector button.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .periode-selector button:hover {
        border-color: #28a745;
        color: #28a745;
    }

    .filter-section {
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .filter-section .row {
        gap: 15px;
        align-items: flex-end;
    }

    .filter-section label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }

    .filter-section input,
    .filter-section select {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 0.95rem;
    }

    .filter-section input:focus,
    .filter-section select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }

    .btn-filter {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .btn-filter:hover {
        background-color: #218838;
    }

    .btn-reset {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .btn-reset:hover {
        background-color: #5a6268;
    }

    .table-container {
        background: white;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    .table-responsive-custom {
        overflow-x: auto;
    }

    .riwayat-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .riwayat-table thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .riwayat-table thead th {
        padding: 12px 8px;
        text-align: center;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .riwayat-table th.produk-col {
        text-align: left;
        position: sticky;
        left: 0;
        background-color: #f8f9fa;
        z-index: 10;
        min-width: 200px;
    }

    .riwayat-table th.stok-awal-col {
        position: sticky;
        left: 200px;
        background-color: #f8f9fa;
        z-index: 10;
        min-width: 100px;
    }

    .riwayat-table tbody td {
        padding: 10px 8px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
        font-size: 0.9rem;
    }

    .riwayat-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .riwayat-table td.produk-col {
        text-align: left;
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 9;
        font-weight: 500;
        color: #333;
    }

    .riwayat-table tbody tr:hover td.produk-col {
        background-color: #f8f9fa;
    }

    .riwayat-table td.stok-awal-col {
        position: sticky;
        left: 200px;
        background-color: white;
        font-weight: 600;
        color: #0066cc;
        border-left: 1px solid #dee2e6;
    }

    .riwayat-table tbody tr:hover td.stok-awal-col {
        background-color: #f8f9fa;
    }

    .date-header-group {
        border-right: 2px solid #dee2e6;
    }

    .date-header-group:last-child {
        border-right: none;
    }

    .date-day {
        font-size: 0.85rem;
        color: #666;
        font-weight: 500;
    }

    .date-date {
        font-size: 0.75rem;
        color: #999;
    }

    .movement-in {
        color: #28a745;
        font-weight: 600;
    }

    .movement-out {
        color: #dc3545;
        font-weight: 600;
    }

    .movement-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 80px;
    }

    .movement-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .barang-code {
        font-size: 0.85rem;
        color: #999;
        display: block;
        margin-top: 3px;
    }

    .info-bar {
        background-color: #e7f3ff;
        border-left: 4px solid #0066cc;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 0.95rem;
        color: #0066cc;
    }

    .info-bar strong {
        color: #0055aa;
    }
</style>

<div class="riwayat-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-graph-up"></i> Riwayat Stok</h2>
            <p class="text-muted mb-0">Analisis pergerakan stok barang per hari</p>
        </div>
    </div>

    <!-- Periode Selector -->
    <div class="periode-selector">
        <form method="GET" action="{{ route('barang-movement.riwayat-stok') }}" id="periodeForm" style="display: flex; gap: 10px;">
            @if($search)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif
            <button type="submit" name="periode" value="1" class="periode-button {{ $periodeDefault == '1' ? 'active' : '' }}">
                1 Bulan
            </button>
            <button type="submit" name="periode" value="3" class="periode-button {{ $periodeDefault == '3' ? 'active' : '' }}">
                3 Bulan
            </button>
            <button type="submit" name="periode" value="6" class="periode-button {{ $periodeDefault == '6' ? 'active' : '' }}">
                6 Bulan
            </button>
            <button type="submit" name="periode" value="12" class="periode-button {{ $periodeDefault == '12' ? 'active' : '' }}">
                1 Tahun
            </button>
        </form>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('barang-movement.riwayat-stok') }}">
            <input type="hidden" name="periode" value="{{ $periodeDefault }}">
            <div class="row">
                <div class="col-md-6">
                    <label>Cari Barang (Nama/Kode)</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Masukkan nama atau kode barang..."
                           value="{{ $search }}">
                </div>
                <div class="col-md-2" style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-filter" style="flex: 1;">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('barang-movement.riwayat-stok') }}" class="btn-reset" style="flex: 1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <strong>Periode:</strong> {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }} | 
        <strong>Total Barang:</strong> {{ count($stockData) }} produk
    </div>

    @if(count($stockData) > 0)
        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive-custom">
                <table class="riwayat-table">
                    <thead>
                        <tr>
                            <th class="produk-col">
                                <i class="bi bi-box"></i> PRODUK
                            </th>
                            <th class="stok-awal-col">STOK AWAL</th>
                            <th style="background-color: #f8f9fa; text-align: center; font-weight: 600; color: #28a745;">
                                MASUK
                            </th>
                            <th style="background-color: #f8f9fa; text-align: center; font-weight: 600; color: #dc3545;">
                                KELUAR
                            </th>
                            <th style="background-color: #f8f9fa; text-align: center; font-weight: 600; color: #0066cc;">
                                STOK SAAT INI
                            </th>
                            @foreach($dates as $date)
                                <th class="date-header-group">
                                    <div class="date-day">
                                        {{ $date->format('D') }}
                                    </div>
                                    <div class="date-date">
                                        {{ $date->format('d/m') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockData as $item)
                            <tr>
                                <td class="produk-col">
                                    {{ $item['barang']->nama_barang }}
                                    <span class="barang-code">Kode: {{ $item['barang']->kode_barang }}</span>
                                </td>
                                <td class="stok-awal-col">
                                    {{ number_format($item['stok_awal'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; font-weight: 600; color: #28a745;">
                                    {{ number_format($item['jumlah_masuk'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; font-weight: 600; color: #dc3545;">
                                    {{ number_format($item['jumlah_keluar'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; font-weight: 600; color: #0066cc;">
                                    {{ number_format($item['stok_saat_ini'], 0, ',', '.') }}
                                </td>
                                @foreach($dates as $date)
                                    <td>
                                        <div class="movement-cell">
                                            @if(isset($item['daily'][$date->format('Y-m-d')]))
                                                @php
                                                    $day = $item['daily'][$date->format('Y-m-d')];
                                                    $masuk = $day['masuk'];
                                                    $keluar = $day['keluar'];
                                                @endphp
                                                @if($masuk > 0 || $keluar > 0)
                                                    <div class="movement-row">
                                                        @if($masuk > 0)
                                                            <span class="movement-in">
                                                                IN: {{ number_format($masuk, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="movement-row">
                                                        @if($keluar > 0)
                                                            <span class="movement-out">
                                                                OUT: {{ number_format($keluar, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span style="color: #ccc;">-</span>
                                                @endif
                                            @else
                                                <span style="color: #ccc;">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5 class="mt-3">Tidak Ada Data</h5>
            <p class="text-muted">Tidak ditemukan riwayat stok untuk periode dan filter yang dipilih.</p>
            <a href="{{ route('barang-movement.riwayat-stok') }}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-arrow-clockwise"></i> Reset Filter
            </a>
        </div>
    @endif
</div>

<script>
    // Make periode buttons work
    document.querySelectorAll('.periode-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
</script>
@endsection
