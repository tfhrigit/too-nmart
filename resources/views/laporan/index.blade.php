@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-earmark-text"></i> Laporan Inventory</h2>
        <div>
            <button class="btn btn-success" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}"
                            max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}"
                            max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Periode Cepat</label>
                        <select class="form-select" id="quickPeriod">
                            <option value="">Pilih Periode</option>
                            <option value="hari_ini">Hari Ini</option>
                            <option value="minggu_ini">Minggu Ini</option>
                            <option value="bulan_ini">Bulan Ini</option>
                            <option value="bulan_lalu">Bulan Lalu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards - DIUBAH LEBIH SEDERHANA -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-title">Total Pembelian</h6>
                    <h4>Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</h4>
                    <small>{{ $summary['jumlah_transaksi_masuk'] }} transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="card-title">Total Penjualan</h6>
                    <h4>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</h4>
                    <small>{{ $summary['jumlah_transaksi_keluar'] }} transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $summary['profit'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Profit</h6>
                    <h4>Rp {{ number_format($summary['profit'], 0, ',', '.') }}</h4>
                    <small>{{ number_format($summary['profit_percentage'], 2) }}% margin</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Ringkasan</h6>
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex justify-content-between">
                            <small>Pembelian:</small>
                            <small>Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>Penjualan:</small>
                            <small>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>Profit:</small>
                            <small class="{{ $summary['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($summary['profit'], 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs - DIUBAH: Hanya 3 tab utama -->
    <ul class="nav nav-tabs mb-3" id="laporanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="barang-masuk-tab" data-bs-toggle="tab" data-bs-target="#barang-masuk"
                type="button">
                <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="barang-keluar-tab" data-bs-toggle="tab" data-bs-target="#barang-keluar"
                type="button">
                <i class="bi bi-box-arrow-up"></i> Barang Keluar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profit-tab" data-bs-toggle="tab" data-bs-target="#profit" type="button">
                <i class="bi bi-graph-up"></i> Analisis Profit
            </button>
        </li>
    </ul>

    <div class="tab-content" id="laporanTabContent">
        <!-- Barang Masuk - DIUBAH LEBIH SEDERHANA -->
        <div class="tab-pane fade show active" id="barang-masuk" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Transaksi Barang Masuk</h5>
                        <span class="badge bg-info">{{ $barangMasukTransaksis->count() }} transaksi</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="120">Tanggal</th>
                                    <th width="150">No. Transaksi</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th width="150" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangMasukTransaksis as $transaksi)
                                    <tr>
                                        <td>
                                            <small class="text-muted">{{ $transaksi->tanggal->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('barang-masuk.show', $transaksi->no_transaksi) }}"
                                                class="text-primary text-decoration-none" title="Lihat detail">
                                                {{ $transaksi->no_transaksi }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $transaksi->supplier->nama_supplier ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($transaksi->items->take(2) as $item)
                                                <div class="mb-1">
                                                    <small>
                                                        {{ $item->barang->nama_barang ?? 'Barang Manual' }}:
                                                        {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                                                    </small>
                                                </div>
                                            @endforeach
                                            @if($transaksi->items->count() > 2)
                                                <small class="text-muted">
                                                    + {{ $transaksi->items->count() - 2 }} item lainnya
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">Tidak ada data barang masuk</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($barangMasukTransaksis->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total Pembelian:</strong></td>
                                        <td class="text-end">
                                            <strong class="text-primary">
                                                Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Keluar - DIUBAH LEBIH SEDERHANA -->
        <div class="tab-pane fade" id="barang-keluar" role="tabpanel">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Transaksi Barang Keluar</h5>
                        <span class="badge bg-success">{{ $barangKeluarTransaksis->count() }} transaksi</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="120">Tanggal</th>
                                    <th width="150">No. Transaksi</th>
                                    <th>Pelanggan</th>
                                    <th>Items</th>
                                    <th width="100" class="text-center">Bayar</th>
                                    <th width="150" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangKeluarTransaksis as $transaksi)
                                    <tr>
                                        <td>
                                            <small class="text-muted">{{ $transaksi->tanggal->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('barang-keluar.show', $transaksi) }}"
                                                class="text-primary text-decoration-none" title="Lihat detail">
                                                {{ $transaksi->no_transaksi }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $transaksi->customer->nama_customer ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($transaksi->items->take(2) as $item)
                                                <div class="mb-1">
                                                    <small>
                                                        {{ $item->barang->nama_barang ?? $item->nama_barang_manual }}:
                                                        {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                                                    </small>
                                                </div>
                                            @endforeach
                                            @if($transaksi->items->count() > 2)
                                                <small class="text-muted">
                                                    + {{ $transaksi->items->count() - 2 }} item lainnya
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge 
                                                                        @if($transaksi->metode_pembayaran == 'cash') bg-success
                                                                        @elseif($transaksi->metode_pembayaran == 'qris') bg-primary
                                                                        @else bg-info @endif">
                                                {{ strtoupper($transaksi->metode_pembayaran) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">
                                                Rp {{ number_format($transaksi->items->sum('total_harga'), 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-cart fs-1"></i>
                                            <p class="mt-2">Tidak ada data barang keluar</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($barangKeluarTransaksis->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Total Penjualan:</strong></td>
                                        <td class="text-end">
                                            <strong class="text-success">
                                                Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analisis Profit - TAB BARU YANG MENGGABUNGKAN -->
        <div class="tab-pane fade" id="profit" role="tabpanel">
            <div class="row">
                <!-- Top Barang -->
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Barang Terlaris (per Transaksi)</h5>
                                <span class="badge bg-primary">{{ count($topBarang) }} item</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">#</th>
                                            <th>Barang & Transaksi</th>
                                            <th class="text-center" width="100">Jumlah</th>
                                            <th class="text-end" width="120">Penjualan</th>
                                            <th class="text-end" width="120">Pembelian</th>
                                            <th class="text-end" width="120">Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topBarang as $index => $item)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <div class="mb-1">
                                                        <strong>{{ $item['nama_barang'] }}</strong>
                                                        <div class="text-muted small">
                                                            Kode: {{ $item['kode_barang'] }}
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('barang-keluar.show', $item['no_transaksi']) }}"
                                                            class="text-primary text-decoration-none small">
                                                            <i class="bi bi-receipt"></i> {{ $item['no_transaksi'] }}
                                                        </a>
                                                        <span class="badge bg-info">{{ $item['customer'] }}</span>
                                                        <span class="badge 
                                                            @if($item['metode_pembayaran'] == 'cash') bg-success
                                                            @elseif($item['metode_pembayaran'] == 'qris') bg-primary
                                                            @else bg-secondary @endif">
                                                            {{ strtoupper($item['metode_pembayaran']) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        {{ $item['tanggal_transaksi']->format('d/m/Y H:i') }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">
                                                        {{ number_format($item['jumlah_terjual'], 2) }}
                                                    </span>
                                                    <div class="text-muted small">
                                                        {{ $item['unit_name'] }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        @ {{ number_format($item['harga_jual'], 0, ',', '.') }}
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="d-block text-success">
                                                        Rp {{ number_format($item['total_penjualan'], 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <div class="text-info">
                                                        Rp {{ number_format($item['total_pembelian'], 0, ',', '.') }}
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        @ Rp {{ number_format($item['harga_beli'], 0, ',', '.') }}
                                                    </small>
                                                </td>
                                                <td class="text-end">
                                                    <div class="{{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                        <strong>Rp {{ number_format($item['profit'], 0, ',', '.') }}</strong>
                                                        @if($item['total_pembelian'] > 0)
                                                            <div class="progress mt-1" style="height: 5px;">
                                                                <div class="progress-bar {{ $item['profit'] >= 0 ? 'bg-success' : 'bg-danger' }}"
                                                                    style="width: {{ min(abs($item['profit_percentage']), 100) }}%">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ number_format($item['profit_percentage'], 1) }}%
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-bar-chart fs-1"></i>
                                                    <p class="mt-2">Belum ada data penjualan</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(count($topBarang) > 0)
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                <td class="text-center">
                                                    <strong>{{ number_format(array_sum(array_column($topBarang, 'jumlah_terjual')), 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-success">
                                                        Rp
                                                        {{ number_format(array_sum(array_column($topBarang, 'total_penjualan')), 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-info">
                                                        Rp
                                                        {{ number_format(array_sum(array_column($topBarang, 'total_pembelian')), 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    @php
                                                        $totalProfit = array_sum(array_column($topBarang, 'profit'));
                                                    @endphp
                                                    <strong class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @if(count($topBarang) > 0)
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> Menampilkan {{ count($topBarang) }} item terlaris
                                            per transaksi
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <small class="text-muted">
                                            Total penjualan: Rp
                                            {{ number_format(array_sum(array_column($topBarang, 'total_penjualan')), 0, ',', '.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ringkasan Profit -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Ringkasan Periode</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <!-- Periode -->
                                <div>
                                    <h6>Periode Laporan</h6>
                                    <div class="p-2 bg-light rounded">
                                        <div class="d-flex justify-content-between">
                                            <small>Mulai:</small>
                                            <small><strong>{{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }}</strong></small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Akhir:</small>
                                            <small><strong>{{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</strong></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penjualan -->
                                <div>
                                    <h6>Total Penjualan</h6>
                                    <h3 class="text-success">
                                        Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
                                    </h3>
                                    <small class="text-muted">{{ $summary['jumlah_transaksi_keluar'] }} transaksi</small>
                                </div>

                                <!-- Pembelian -->
                                <div>
                                    <h6>Total Pembelian</h6>
                                    <h4 class="text-info">
                                        Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">Harga beli barang yang terjual</small>
                                </div>

                                <!-- Profit -->
                                <div>
                                    <h6>Profit/Laba</h6>
                                    <h2 class="{{ $summary['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($summary['profit'], 0, ',', '.') }}
                                    </h2>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $summary['profit'] >= 0 ? 'bg-success' : 'bg-danger' }}"
                                            style="width: {{ min(abs($summary['profit_percentage']), 100) }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Margin:</small>
                                        <small
                                            class="text-muted">{{ number_format($summary['profit_percentage'], 2) }}%</small>
                                    </div>
                                </div>

                                <!-- Statistik Item -->
                                @if(count($topBarang) > 0)
                                    <div class="mt-2">
                                        <h6>Statistik Item Terlaris</h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="d-block text-muted">Total Item</small>
                                                    <strong>{{ count($topBarang) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="d-block text-muted">Total Jumlah</small>
                                                    <strong>{{ number_format(array_sum(array_column($topBarang, 'jumlah_terjual')), 2) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="d-block text-muted">Rata Profit</small>
                                                    <strong
                                                        class="{{ (array_sum(array_column($topBarang, 'profit')) / count($topBarang)) >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp
                                                        {{ number_format(array_sum(array_column($topBarang, 'profit')) / count($topBarang), 0, ',', '.') }}
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="d-block text-muted">Avg Margin</small>
                                                    <strong>
                                                        @php
                                                            $avgMargin = count($topBarang) > 0 ?
                                                                array_sum(array_column($topBarang, 'profit_percentage')) / count($topBarang) : 0;
                                                        @endphp
                                                        {{ number_format($avgMargin, 1) }}%
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit per Transaksi -->
            @if(count($profitPerTransaksi) > 0)
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Profit per Transaksi</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="150">Transaksi</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-end" width="120">Penjualan</th>
                                        <th class="text-end" width="120">Pembelian</th>
                                        <th class="text-end" width="120">Profit</th>
                                        <th width="80">Margin</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($profitPerTransaksi as $item)
                                        @php
                                            $profitPercentage = $item['total_pembelian'] > 0
                                                ? ($item['profit'] / $item['total_pembelian']) * 100
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <a href="{{ route('barang-keluar.show', $item['no_transaksi']) }}"
                                                        class="text-primary text-decoration-none">
                                                        <strong>{{ $item['no_transaksi'] }}</strong>
                                                    </a>
                                                    <div class="text-muted small">
                                                        {{ $item['tanggal']->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $item['item_count'] }} item</span>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">Rp
                                                    {{ number_format($item['total_penjualan'], 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-info">Rp
                                                    {{ number_format($item['total_pembelian'], 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <strong class="{{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($item['profit'], 0, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $profitPercentage >= 20 ? 'bg-success' : ($profitPercentage >= 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ number_format($profitPercentage, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                        @if($item['metode_pembayaran'] == 'cash') bg-success
                                                        @elseif($item['metode_pembayaran'] == 'qris') bg-primary
                                                        @else bg-info @endif">
                                                    {{ strtoupper($item['metode_pembayaran']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .table td {
            font-size: 0.875rem;
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
        }

        .card-header {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .progress {
            border-radius: 5px;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .text-muted {
            font-size: 0.8rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Aktifkan tab pertama
        document.addEventListener('DOMContentLoaded', function () {
            var triggerTabList = [].slice.call(document.querySelectorAll('#laporanTab button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });

        // Filter periode cepat
        document.getElementById('quickPeriod').addEventListener('change', function () {
            const today = new Date();
            const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
            const tanggalAkhir = document.querySelector('input[name="tanggal_akhir"]');

            switch (this.value) {
                case 'hari_ini':
                    const todayStr = today.toISOString().split('T')[0];
                    tanggalMulai.value = todayStr;
                    tanggalAkhir.value = todayStr;
                    break;
                case 'minggu_ini':
                    const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                    const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    tanggalMulai.value = firstDay.toISOString().split('T')[0];
                    tanggalAkhir.value = lastDay.toISOString().split('T')[0];
                    break;
                case 'bulan_ini':
                    const firstDayMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDayMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    tanggalMulai.value = firstDayMonth.toISOString().split('T')[0];
                    tanggalAkhir.value = lastDayMonth.toISOString().split('T')[0];
                    break;
                case 'bulan_lalu':
                    const firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    tanggalMulai.value = firstDayLastMonth.toISOString().split('T')[0];
                    tanggalAkhir.value = lastDayLastMonth.toISOString().split('T')[0];
                    break;
            }

            // Submit form otomatis
            if (this.value) {
                document.getElementById('filterForm').submit();
            }
        });

        // Batasi tanggal
        document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', function () {
            document.querySelector('input[name="tanggal_akhir"]').min = this.value;
        });

        document.querySelector('input[name="tanggal_akhir"]').addEventListener('change', function () {
            document.querySelector('input[name="tanggal_mulai"]').max = this.value;
        });
    </script>
@endpush