<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barang Keluar - {{ $barangKeluar->no_transaksi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        
        .total-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #000;
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
        
        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        
        .badge-primary {
            background-color: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .badge-secondary {
            background-color: #e2e3e5;
            color: #41464b;
            border: 1px solid #d3d6d8;
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
            
            .page-break {
                page-break-before: always;
            }
            
            /* Ensure badges are visible in print */
            .badge-print {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
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
        <div class="receipt-title">NOTA BARANG KELUAR</div>
        
        <!-- Transaction Info -->
        <div class="row info-section">
            <div class="col-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">No. Transaksi</td>
                        <td>: {{ $barangKeluar->no_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td>: {{ $barangKeluar->tanggal->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Pelanggan</td>
                        <td>: 
                            @if($barangKeluar->customer)
                                {{ $barangKeluar->customer->nama_customer }}
                            @else
                                Umum
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Kode Pelanggan</td>
                        <td>: {{ $barangKeluar->customer->kode_customer ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="info-label">Metode Pembayaran</td>
                        <td>: 
                            <span class="badge-print 
                                @if($barangKeluar->metode_pembayaran == 'cash') badge-success
                                @elseif($barangKeluar->metode_pembayaran == 'qris') badge-primary
                                @else badge-info @endif">
                                {{ strtoupper($barangKeluar->metode_pembayaran) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Jumlah Item</td>
                        <td>: {{ $barangKeluar->items->count() }} barang</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kasir</td>
                        <td>: {{ $barangKeluar->user->name }}</td>
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
                    <th width="35%">Nama Barang</th>
                    <th width="15%" class="text-center">Jumlah</th>
                    <th width="15%" class="text-right">Harga Satuan</th>
                    <th width="15%" class="text-right">Total</th>
                    <th width="15%" class="text-center">Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangKeluar->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($item->barang_id)
                            {{ $item->barang->nama_barang }}
                            @if($item->barang->kode_barang)
                                <br><small>Kode: {{ $item->barang->kode_barang }}</small>
                            @endif
                            @if($item->barang->stok_sekarang)
                                <br><small>Stok: {{ $item->barang->stok_sekarang }} {{ $item->barang->base_unit }}</small>
                            @endif
                        @else
                            {{ $item->nama_barang_manual }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                        @if($item->barang_id && $item->jumlah_in_base_unit)
                            <br><small>= {{ number_format($item->jumlah_in_base_unit, 2) }} {{ $item->barang->base_unit }}</small>
                        @endif
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge-print 
                            @if($barangKeluar->metode_pembayaran == 'cash') badge-success
                            @elseif($barangKeluar->metode_pembayaran == 'qris') badge-primary
                            @else badge-info @endif">
                            {{ strtoupper($barangKeluar->metode_pembayaran) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
                    <td colspan="2" class="text-right">
                        <strong>Rp {{ number_format($barangKeluar->total_transaksi, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Metode Pembayaran Detail -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body p-2" style="border: 1px solid #dee2e6;">
                        <h6 class="mb-2"><strong>Detail Pembayaran:</strong></h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Metode Pembayaran:</strong></td>
                                <td>
                                    @if($barangKeluar->metode_pembayaran == 'cash')
                                        <i class="bi bi-cash-coin"></i> Pembayaran Tunai
                                    @elseif($barangKeluar->metode_pembayaran == 'qris')
                                        <i class="bi bi-qr-code-scan"></i> Pembayaran QRIS
                                    @elseif($barangKeluar->metode_pembayaran == 'transfer')
                                        <i class="bi bi-bank"></i> Transfer Bank
                                    @endif
                                    ({{ ucfirst($barangKeluar->metode_pembayaran) }})
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge-print badge-success">
                                        @if($barangKeluar->metode_pembayaran == 'cash')
                                            LUNAS
                                        @else
                                            SELESAI
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        @if($barangKeluar->keterangan)
        <div class="mt-4">
            <strong>Keterangan:</strong><br>
            {{ $barangKeluar->keterangan }}
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
                        <div class="text-center">Kasir</div>
                    </div>
                    <div class="col-4">
                        <div class="signature-line"></div>
                        <div class="text-center">Pelanggan</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <em>** Terima kasih atas kepercayaan Anda **</em><br>
                <small>Nota ini dicetak secara otomatis pada {{ now()->format('d F Y H:i:s') }}</small><br>
                <small>Metode Pembayaran: {{ strtoupper($barangKeluar->metode_pembayaran) }}</small>
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