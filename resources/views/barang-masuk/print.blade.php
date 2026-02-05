<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barang Masuk - {{ $transaksi->no_transaksi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12pt;
            color: #000;
            background: white;
        }
        
        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11pt;
            margin-bottom: 10px;
        }
        
        .receipt-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10pt;
        }
        
        .table-print th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .table-print td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        
        .table-print tfoot {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10pt;
        }
        
        .signature-section {
            margin-top: 60px;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 40px auto 5px;
        }
        
        .badge-print {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header Company -->
        <div class="header">
            <div class="company-name">TOKO ANDA</div>
            <div class="company-address">
                Jl. Contoh No. 123, Kota Anda<br>
                Telp: (021) 12345678 | Email: info@tokoanda.com
            </div>
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">NOTA BARANG MASUK</div>
        
        <!-- Transaction Info -->
        <div class="row info-section">
            <div class="col-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">No. Transaksi</td>
                        <td>: {{ $transaksi->no_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td>: {{ $transaksi->tanggal_formatted }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Supplier</td>
                        <td>: {{ $transaksi->supplier->nama_supplier }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kode Supplier</td>
                        <td>: {{ $transaksi->supplier->kode_supplier }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">Invoice Supplier</td>
                        <td>: {{ $transaksi->invoice_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Jumlah Item</td>
                        <td>: {{ $transaksi->items->count() }} barang</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kasir</td>
                        <td>: {{ $transaksi->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Waktu Cetak</td>
                        <td>: {{ now()->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="table-print">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Nama Barang</th>
                    <th width="15%" class="text-center">Jumlah</th>
                    <th width="20%" class="text-right">Harga Beli</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->barang->nama_barang ?? $item->nama_barang_manual }}
                        @if($item->barang)
                            <br><small>Kode: {{ $item->barang->kode_barang }}</small>
                            @if($item->barang->stok_sekarang)
                                <br><small>Stok: {{ $item->barang->stok_sekarang }} {{ $item->barang->base_unit }}</small>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">
                        {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                        @if($item->barang && $item->unit_name !== $item->barang->base_unit)
                            <br><small>= {{ number_format($item->jumlah_in_base_unit ?? $item->jumlah, 2) }} {{ $item->barang->base_unit }}</small>
                        @endif
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
                    <td colspan="2" class="text-right">
                        <strong>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Notes -->
        @if($transaksi->keterangan)
        <div class="mt-4">
            <strong>Keterangan:</strong><br>
            {{ $transaksi->keterangan }}
        </div>
        @endif
        
        <!-- Footer & Signature -->
        <div class="footer">
            <div class="signature-section">
                <div class="row">
                    <div class="col-4">
                        <div class="signature-line"></div>
                        <div class="text-center">Hormat Kami</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-line"></div>
                        <div class="text-center">Penerima</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-line"></div>
                        <div class="text-center">Supplier</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <em>** Terima kasih atas kerjasamanya **</em><br>
                <small>Dokumen ini dicetak secara otomatis pada {{ now()->format('d F Y H:i:s') }}</small>
            </div>
        </div>
    </div>
    
    <!-- Print Script -->
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
            
            // Return to previous page after printing
            setTimeout(function() {
                window.history.back();
            }, 1000);
        };
        
        // Fallback if user cancels print
        window.onafterprint = function() {
            setTimeout(function() {
                window.history.back();
            }, 500);
        };
    </script>
</body>
</html>