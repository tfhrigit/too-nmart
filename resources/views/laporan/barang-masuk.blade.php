// resources/views/laporan/barang-masuk.blade.php
@extends('layouts.app')

@section('title', 'Laporan Barang Masuk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-in-down"></i> Laporan Barang Masuk</h2>
    <div class="btn-group">
        <a href="{{ route('laporan.export.barang-masuk', [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir
        ]) }}" class="btn btn-danger">
            <i class="bi bi-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('laporan.export.excel.barang-masuk', [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir
        ]) }}" class="btn btn-success">
            <i class="bi bi-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.barang-masuk') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" 
                           value="{{ $tanggalMulai }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" 
                           value="{{ $tanggalAkhir }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi</h6>
                <h4>{{ $summary['jumlah_transaksi'] }}</h4>
                <small>Jumlah transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Total Item</h6>
                <h4>{{ $summary['total_item'] }}</h4>
                <small>Total item barang</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title">Total Pembelian</h6>
                <h4>Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</h4>
                <small>Nilai total pembelian</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Rata-rata</h6>
                <h4>Rp {{ number_format($summary['jumlah_transaksi'] > 0 ? $summary['total_pembelian'] / $summary['jumlah_transaksi'] : 0, 0, ',', '.') }}</h4>
                <small>Rata-rata per transaksi</small>
            </div>
        </div>
    </div>
</div>

<!-- Detail Laporan -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            Detail Barang Masuk
            <small class="text-muted">({{ $tanggalMulai }} s/d {{ $tanggalAkhir }})</small>
        </h5>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Supplier</th>
                        <th>Invoice Supplier</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalGrandTotal = 0;
                        $rowNumber = 1;
                    @endphp
                    
                    @foreach($transaksis as $transaksi)
                        @foreach($transaksi->items as $index => $item)
                            <tr>
                                <td>{{ $rowNumber++ }}</td>
                                @if($index === 0)
                                    <td rowspan="{{ $transaksi->items->count() }}">
                                        {{ $transaksi->tanggal_formatted }}
                                    </td>
                                    <td rowspan="{{ $transaksi->items->count() }}">
                                        <strong>{{ $transaksi->no_transaksi }}</strong>
                                    </td>
                                    <td rowspan="{{ $transaksi->items->count() }}">
                                        {{ $transaksi->supplier->nama_supplier }}
                                    </td>
                                    <td rowspan="{{ $transaksi->items->count() }}">
                                        {{ $transaksi->invoice_supplier ?? '-' }}
                                    </td>
                                @endif
                                <td>
                                    {{ $item->barang->nama_barang }}
                                    <small class="text-muted">({{ $item->barang->kode_barang }})</small>
                                </td>
                                <td>
                                    {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        @php
                            $totalGrandTotal += $transaksi->grand_total;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="8" class="text-end">TOTAL KESELURUHAN:</th>
                        <th class="text-end text-primary">
                            Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($transaksis->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                Tidak ada data transaksi pada periode yang dipilih
            </div>
        @endif
    </div>
</div>

<!-- Chart Section (Optional) -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Trend Pembelian per Hari</h5>
                <div id="chartBarangMasuk" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Distribusi per Supplier</h5>
                <div id="chartSupplier" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    table tbody tr td[rowspan] {
        vertical-align: middle;
    }
    
    .table tfoot th {
        background-color: #f8f9fa;
        font-size: 1.1em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {
        // Data untuk chart (contoh)
        const chartData = {
            series: [{
                name: 'Total Pembelian',
                data: [4000000, 3500000, 4500000, 5000000, 4800000, 5200000]
            }],
            categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
        };

        // Inisialisasi chart
        const options = {
            chart: {
                type: 'line',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            series: chartData.series,
            xaxis: {
                categories: chartData.categories
            },
            colors: ['#007bff']
        };

        const chart = new ApexCharts(document.querySelector("#chartBarangMasuk"), options);
        chart.render();
    });
</script>
@endpush