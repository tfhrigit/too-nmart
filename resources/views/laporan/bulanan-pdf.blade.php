@php
    use App\Models\BarangKeluar;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapan Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            color: #333;
        }
        .header h3 {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .summary-box {
            display: inline-block;
            width: 32%;
            padding: 8px;
            margin: 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: top;
            background: white;
        }
        .summary-box .value {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
        .summary-box .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            page-break-inside: auto;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px 8px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2c3e50;
            color: #2c3e50;
        }
        .profit-positive {
            color: #28a745;
            font-weight: bold;
        }
        .profit-negative {
            color: #dc3545;
            font-weight: bold;
        }
        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: right;
        }
        .page-break {
            page-break-before: always;
        }
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background: #f8f9fa;
        }
        .chart-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .chart-legend {
            margin-top: 10px;
            font-size: 10px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 15px;
        }
        .legend-color {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📊 LAPORAN REKAPAN BULANAN</h2>
        <h3>Toko Bangunan - Tahun {{ $tahun }}</h3>
        <small>Dicetak: {{ $tanggalCetak }}</small>
    </div>
    
    <!-- Ringkasan Tahun -->
    <div class="summary">
        <div class="text-center" style="margin-bottom: 15px;">
            <strong style="font-size: 12px; color: #2c3e50;">💼 RINGKASAN KINERJA TAHUN {{ $tahun }} 💼</strong>
        </div>
        <div style="text-align: center;">
            <div class="summary-box">
                <div class="label">💰 Total Penjualan</div>
                <div class="value" style="color: #28a745;">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">📦 Total Pembelian</div>
                <div class="value" style="color: #dc3545;">Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">🎯 Total Profit</div>
                <div class="value {{ $summary['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    Rp {{ number_format($summary['profit'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-box">
                <div class="label">💵 Total Cash</div>
                <div class="value" style="color: #17a2b8;">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">📱 Total QRIS</div>
                <div class="value" style="color: #0d6efd;">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="label">📈 Margin Profit</div>
                <div class="value {{ $summary['profit_percentage'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ number_format($summary['profit_percentage'], 2) }}%
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart Ringkasan Grafik (Text-based) -->
    <div class="chart-container">
        <div class="chart-title">📊 ANALISIS PEMBELIAN & PENJUALAN TAHUN {{ $tahun }}</div>
        <table style="font-size: 9px; margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 15%;">Bulan</th>
                    @foreach($chartData['labels'] as $label)
                        <th class="text-center" style="width: 7%;">{{ substr($label, 0, 3) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #fff3cd;">
                    <td><strong>📥 Pembelian</strong></td>
                    @foreach($chartData['pembelian'] as $value)
                        <td class="text-right">{{ number_format($value / 1000000, 1) }}M</td>
                    @endforeach
                </tr>
                <tr style="background-color: #d1ecf1;">
                    <td><strong>📤 Penjualan</strong></td>
                    @foreach($chartData['penjualan'] as $value)
                        <td class="text-right">{{ number_format($value / 1000000, 1) }}M</td>
                    @endforeach
                </tr>
                <tr style="background-color: #d4edda;">
                    <td><strong>🎯 Profit</strong></td>
                    @foreach($chartData['profit'] as $value)
                        <td class="text-right {{ $value >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            {{ number_format($value / 1000000, 1) }}M
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <div class="chart-legend">
            <div class="legend-item"><span class="legend-color" style="background-color: #ffc107;"></span>Pembelian</div>
            <div class="legend-item"><span class="legend-color" style="background-color: #17a2b8;"></span>Penjualan</div>
            <div class="legend-item"><span class="legend-color" style="background-color: #28a745;"></span>Profit Positif</div>
        </div>
    </div>
    
    <!-- Tabel Rekapan Bulanan -->
    <div class="section-title">📋 DETAIL REKAPAN BULANAN TAHUN {{ $tahun }}</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Bulan</th>
                <th width="13%" class="text-right">Pembelian</th>
                <th width="13%" class="text-right">Penjualan</th>
                <th width="11%" class="text-right">Cash</th>
                <th width="11%" class="text-right">QRIS</th>
                <th width="11%" class="text-center">Transaksi</th>
                <th width="12%" class="text-right">Profit</th>
                <th width="10%" class="text-center">Margin</th>
                <th width="9%" class="text-center">Rating</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPembelian = 0;
                $totalPenjualan = 0;
                $totalCash = 0;
                $totalQris = 0;
                $totalProfit = 0;
            @endphp
            
            @foreach($monthlyReports as $report)
                @php
                    $totalPembelian += $report->total_pembelian;
                    $totalPenjualan += $report->total_penjualan;
                    $totalCash += $report->total_penjualan_cash;
                    $totalQris += $report->total_penjualan_qris;
                    $totalProfit += $report->profit;
                @endphp
                <tr>
                    <td><strong>{{ strtoupper(substr($report->bulan_name, 0, 3)) }}</strong></td>
                    <td class="text-right">Rp {{ number_format($report->total_pembelian, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #28a745; font-weight: bold;">Rp {{ number_format($report->total_penjualan, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <span class="badge badge-success">Rp {{ number_format($report->total_penjualan_cash, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <span class="badge badge-primary">Rp {{ number_format($report->total_penjualan_qris, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-center">
                        <small>{{ $report->jumlah_transaksi_masuk }}/{{ $report->jumlah_transaksi_keluar }}</small>
                    </td>
                    <td class="text-right {{ $report->profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        <strong>Rp {{ number_format($report->profit, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $report->profit_percentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ number_format($report->profit_percentage, 1) }}%
                        </span>
                    </td>
                    <td class="text-center" style="font-weight: bold; font-size: 12px;">
                        @if($report->profit_percentage >= 25)
                            ⭐⭐⭐
                        @elseif($report->profit_percentage >= 15)
                            ⭐⭐
                        @elseif($report->profit_percentage >= 5)
                            ⭐
                        @else
                            ⚠️
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #f1f1f1; font-weight: bold; border-top: 3px solid #2c3e50;">
            <tr>
                <td><strong>💰 TOTAL {{ $tahun }}</strong></td>
                <td class="text-right">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #28a745;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalQris, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-right {{ $totalProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    <strong>Rp {{ number_format($totalProfit, 0, ',', '.') }}</strong>
                </td>
                <td class="text-center">
                    <span class="badge {{ $totalPembelian > 0 && ($totalProfit / $totalPembelian) * 100 >= 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ $totalPembelian > 0 ? number_format(($totalProfit / $totalPembelian) * 100, 1) : 0 }}%
                    </span>
                </td>
                <td class="text-center">
                    @php
                        $marginTahun = $totalPembelian > 0 ? ($totalProfit / $totalPembelian) * 100 : 0;
                    @endphp
                    @if($marginTahun >= 25)
                        ✅ EXCELLENT
                    @elseif($marginTahun >= 15)
                        ✅ GOOD
                    @elseif($marginTahun >= 5)
                        ⚠️ FAIR
                    @else
                        ❌ LOSS
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
    
    @if(count($summary['top_barang']) > 0)
        <div class="page-break"></div>
        
        <div class="header">
            <h2>TOP 10 BARANG TERLARIS TAHUN {{ $tahun }}</h2>
            <small>Dicetak: {{ $tanggalCetak }}</small>
        </div>
        
        <div class="section-title">BARANG TERLARIS TAHUN {{ $tahun }}</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Nama Barang</th>
                    <th width="20%" class="text-center">Total Terjual</th>
                    <th width="15%" class="text-right">Harga Rata-rata</th>
                    <th width="15%" class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['top_barang'] as $index => $item)
                    @php
                        $averagePrice = $item->total_terjual > 0 
                            ? $item->total_penjualan / $item->total_terjual 
                            : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td class="text-center">{{ number_format($item->total_terjual, 2) }} {{ $item->barang->base_unit }}</td>
                        <td class="text-right">Rp {{ number_format($averagePrice, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Ringkasan Metode Pembayaran -->
        <div class="section-title" style="margin-top: 30px;">RINGKASAN METODE PEMBAYARAN</div>
        <table>
            <thead>
                <tr>
                    <th width="50%">Metode Pembayaran</th>
                    <th width="25%" class="text-right">Jumlah Transaksi</th>
                    <th width="25%" class="text-right">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cashTransactions = BarangKeluar::whereYear('tanggal', $tahun)
                        ->where('metode_pembayaran', 'cash')
                        ->count();
                    
                    $qrisTransactions = BarangKeluar::whereYear('tanggal', $tahun)
                        ->where('metode_pembayaran', 'qris')
                        ->count();
                    
                    $totalTransactions = $cashTransactions + $qrisTransactions;
                @endphp
                <tr>
                    <td><strong>CASH</strong></td>
                    <td class="text-right">{{ number_format($cashTransactions, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>QRIS</strong></td>
                    <td class="text-right">{{ number_format($qrisTransactions, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right">{{ number_format($totalTransactions, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="chart-container" style="margin-top: 20px;">
            <div class="chart-title">PERBANDINGAN METODE PEMBAYARAN TAHUN {{ $tahun }}</div>
            <table style="width: 100%;">
                <tr>
                    <td width="40%">
                        <strong>CASH:</strong><br>
                        <span style="font-size: 18px; font-weight: bold; color: #28a745;">
                            {{ $totalTransactions > 0 ? number_format(($cashTransactions / $totalTransactions) * 100, 1) : 0 }}%
                        </span><br>
                        <small>{{ number_format($cashTransactions, 0, ',', '.') }} transaksi</small>
                    </td>
                    <td width="20%" class="text-center" style="font-size: 24px;">VS</td>
                    <td width="40%">
                        <strong>QRIS:</strong><br>
                        <span style="font-size: 18px; font-weight: bold; color: #007bff;">
                            {{ $totalTransactions > 0 ? number_format(($qrisTransactions / $totalTransactions) * 100, 1) : 0 }}%
                        </span><br>
                        <small>{{ number_format($qrisTransactions, 0, ',', '.') }} transaksi</small>
                    </td>
                </tr>
            </table>
        </div>
    @endif
    
    <!-- Footer Profesional -->
    <div class="footer" style="margin-top: 40px; border-top: 2px solid #2c3e50; padding-top: 15px;">
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td width="33%">
                    <strong>Disiapkan oleh:</strong><br>
                    Sistem Inventory Otomatis<br>
                    {{ $tanggalCetak }}
                </td>
                <td width="34%" style="text-align: center;">
                    <strong>Catatan Penting:</strong><br>
                    ✓ Data terhitung sampai {{ $tanggalCetak }}<br>
                    ✓ Dokumen ini bersifat confidential
                </td>
                <td width="33%" style="text-align: right;">
                    <strong>Periode Laporan:</strong><br>
                    {{ \Carbon\Carbon::create($tahun, 1, 1)->format('d M Y') }} s/d<br>
                    {{ \Carbon\Carbon::create($tahun, 12, 31)->format('d M Y') }}
                </td>
            </tr>
        </table>
        <div style="margin-top: 10px; font-size: 9px; color: #999; text-align: center;">
            🔒 Laporan ini dibuat secara otomatis oleh Sistem Inventory Toko Bangunan • Versi 1.0
        </div>
    </div>
</body>
</html>