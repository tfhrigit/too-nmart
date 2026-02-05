<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .summary {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f0f0f0;
            padding: 6px;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 4px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
        }
        .page-break {
            page-break-before: always;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .item-detail {
            font-size: 8px;
            margin: 1px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 5px 0;">LAPORAN INVENTORY TOKO BANGUNAN</h2>
        <p style="margin: 3px 0;">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y') }}</p>
        <small>Dicetak: {{ now()->format('d F Y H:i') }}</small>
    </div>
    
    <div class="summary">
        <table style="width: 70%; margin: 0 auto;">
            <tr>
                <td width="60%"><strong>Total Pembelian:</strong></td>
                <td width="40%" class="text-right">Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Penjualan:</strong></td>
                <td class="text-right">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Profit/Loss:</strong></td>
                <td class="text-right">Rp {{ number_format($summary['profit'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Margin Profit:</strong></td>
                <td class="text-right">{{ number_format($summary['profit_percentage'], 2) }}%</td>
            </tr>
        </table>
    </div>
    
    <div class="section-title">BARANG MASUK</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="15%">No. Transaksi</th>
                <th width="15%">Supplier</th>
                <th width="40%">Items</th>
                <th width="10%">Items</th>
                <th width="10%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangMasukTransaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                <td>{{ $transaksi->no_transaksi }}</td>
                <td>{{ $transaksi->supplier->nama_supplier ?? '-' }}</td>
                <td>
                    @foreach($transaksi->items as $item)
                    <div class="item-detail">
                        • {{ $item->barang->nama_barang ?? 'Barang Manual' }}: {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                        @ Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </div>
                    @endforeach
                </td>
                <td class="text-center">{{ $transaksi->items->count() }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL {{ $barangMasukTransaksis->count() }} TRANSAKSI:</strong></td>
                <td class="text-center"><strong>{{ $barangMasukTransaksis->sum(fn($t) => $t->items->count()) }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_pembelian'], 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="section-title">BARANG KELUAR</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="15%">No. Transaksi</th>
                <th width="15%">Pelanggan</th>
                <th width="30%">Items</th>
                <th width="10%">Bayar</th>
                <th width="10%">Items</th>
                <th width="10%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangKeluarTransaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                <td>{{ $transaksi->no_transaksi }}</td>
                <td>{{ $transaksi->customer->nama_customer ?? 'Umum' }}</td>
                <td>
                    @foreach($transaksi->items as $item)
                    <div class="item-detail">
                        • {{ $item->barang->nama_barang ?? $item->nama_barang_manual }}: {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                        @ Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </div>
                    @endforeach
                </td>
                <td class="text-center">{{ strtoupper($transaksi->metode_pembayaran) }}</td>
                <td class="text-center">{{ $transaksi->items->count() }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->items->sum('total_harga'), 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL {{ $barangKeluarTransaksis->count() }} TRANSAKSI:</strong></td>
                <td class="text-center"><strong>{{ $barangKeluarTransaksis->sum(fn($t) => $t->items->count()) }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="section-title">TOP 10 BARANG TERLARIS</div>
    <table>
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="50%">Nama Barang</th>
                <th width="20%">Total Terjual</th>
                <th width="20%" class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topBarang as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>{{ number_format($item->total_terjual, 2) }} {{ $item->barang->base_unit }}</td>
                <td class="text-right">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Summary Pembayaran -->
    <div class="section-title">RINGKASAN PEMBAYARAN</div>
    <table style="width: 50%;">
        <tr>
            <td><strong>Cash:</strong></td>
            <td class="text-right">Rp {{ number_format($summaryPembayaran['total_cash'], 0, ',', '.') }}</td>
            <td class="text-right">({{ number_format($summaryPembayaran['persentase_cash'], 1) }}%)</td>
        </tr>
        <tr>
            <td><strong>QRIS:</strong></td>
            <td class="text-right">Rp {{ number_format($summaryPembayaran['total_qris'], 0, ',', '.') }}</td>
            <td class="text-right">({{ number_format($summaryPembayaran['persentase_qris'], 1) }}%)</td>
        </tr>
        <tr>
            <td><strong>Transfer:</strong></td>
            <td class="text-right">Rp {{ number_format($summaryPembayaran['total_transfer'], 0, ',', '.') }}</td>
            <td class="text-right">({{ number_format($summaryPembayaran['persentase_transfer'], 1) }}%)</td>
        </tr>
        <tr class="total-row">
            <td><strong>TOTAL:</strong></td>
            <td class="text-right" colspan="2">
                <strong>Rp {{ number_format($summaryPembayaran['total_cash'] + $summaryPembayaran['total_qris'] + $summaryPembayaran['total_transfer'], 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>