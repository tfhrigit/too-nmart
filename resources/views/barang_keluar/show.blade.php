@extends('layouts.app')

@section('title', 'Detail Barang Keluar: ' . $barangKeluar->no_transaksi)

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt"></i> Detail Barang Keluar: {{ $barangKeluar->no_transaksi }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Header Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Tanggal</th>
                                <td>: {{ $barangKeluar->tanggal->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>No. Transaksi</th>
                                <td>: {{ $barangKeluar->no_transaksi }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td>: 
                                    @if($barangKeluar->customer)
                                    {{ $barangKeluar->customer->nama_customer }}
                                    @else
                                    Umum
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kode Pelanggan</th>
                                <td>: {{ $barangKeluar->customer->kode_customer ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>: {{ $barangKeluar->customer->no_hp ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Metode Pembayaran</th>
                                <td>: 
                                    <span class="badge {{ $barangKeluar->metode_pembayaran == 'cash' ? 'bg-success' : ($barangKeluar->metode_pembayaran == 'qris' ? 'bg-primary' : 'bg-info') }}">
                                        {{ strtoupper($barangKeluar->metode_pembayaran) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Jumlah Item</th>
                                <td>: {{ $barangKeluar->items->count() }} barang</td>
                            </tr>
                            <tr>
                                <th>Input Oleh</th>
                                <td>: {{ $barangKeluar->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Input</th>
                                <td>: {{ $barangKeluar->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Barang</th>
                                <th width="15%" class="text-center">Jumlah</th>
                                <th width="20%" class="text-end">Harga Satuan</th>
                                <th width="20%" class="text-end">Total</th>
                                <th width="10%" class="text-center">Metode Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangKeluar->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">
                                        @if($item->barang_id)
                                        {{ $item->barang->nama_barang }}
                                        @else
                                        {{ $item->nama_barang_manual }}
                                        @endif
                                    </div>
                                    @if($item->barang)
                                    <div class="small text-muted">
                                        Kode: {{ $item->barang->kode_barang }}
                                        @if($item->barang->stok_sekarang)
                                            | Stok: {{ $item->barang->stok_sekarang }} {{ $item->barang->base_unit }}
                                        @endif
                                    </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">
                                        {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                                    </span>
                                    @if($item->barang_id)
                                    <div class="small text-muted">
                                        = {{ number_format($item->jumlah_in_base_unit, 2) }} {{ $item->barang->base_unit }}
                                    </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $barangKeluar->metode_pembayaran == 'cash' ? 'bg-success' : ($barangKeluar->metode_pembayaran == 'qris' ? 'bg-primary' : 'bg-info') }}">
                                        {{ strtoupper($barangKeluar->metode_pembayaran) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Keseluruhan:</th>
                                <th colspan="2" class="text-end text-success fs-5">
                                    Rp {{ number_format($barangKeluar->total_transaksi, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Tambahan informasi untuk mencocokkan dengan print -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Tambahan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="50%">Waktu Cetak</th>
                                        <td>: {{ now()->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status Cetak</th>
                                        <td>: 
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-printer"></i> Siap Cetak
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-credit-card"></i> Metode Pembayaran Detail</h6>
                            </div>
                            <div class="card-body">
                                @if($barangKeluar->metode_pembayaran == 'cash')
                                <p class="mb-1"><i class="bi bi-cash-coin text-success"></i> Pembayaran Tunai</p>
                                @elseif($barangKeluar->metode_pembayaran == 'qris')
                                <p class="mb-1"><i class="bi bi-qr-code-scan text-primary"></i> Pembayaran QRIS</p>
                                @elseif($barangKeluar->metode_pembayaran == 'transfer')
                                <p class="mb-1"><i class="bi bi-bank text-info"></i> Transfer Bank</p>
                                @endif
                                <p class="mb-0 text-muted small">Metode: {{ ucfirst($barangKeluar->metode_pembayaran) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($barangKeluar->keterangan)
                <div class="alert alert-light mb-4">
                    <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Keterangan:</h6>
                    <p class="mb-0">{{ $barangKeluar->keterangan }}</p>
                </div>
                @endif

                <!-- Actions -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <a href="{{ route('barang-keluar.print', $barangKeluar) }}" 
                           target="_blank" 
                           class="btn btn-outline-success ms-2 no-print">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                    </div>
                    <div class="btn-group no-print">
                        <a href="{{ route('barang-keluar.edit', $barangKeluar) }}" 
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('barang-keluar.destroy', $barangKeluar) }}" 
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus transaksi {{ $barangKeluar->no_transaksi }}? Stok akan dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Signature section untuk print -->
                <div class="d-none d-print-block signature-section mt-5">
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="signature-line" style="width: 200px; border-top: 1px solid #000; margin: 40px auto 5px;"></div>
                            <div>Hormat Kami</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="signature-line" style="width: 200px; border-top: 1px solid #000; margin: 40px auto 5px;"></div>
                            <div>Kasir</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="signature-line" style="width: 200px; border-top: 1px solid #000; margin: 40px auto 5px;"></div>
                            <div>Pelanggan</div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <em>** Terima kasih atas kepercayaan Anda **</em><br>
                        <small>Nota ini dicetak secara otomatis pada {{ now()->format('d F Y H:i:s') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print, .no-print * {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background-color: #fff !important;
        color: #000 !important;
        border-bottom: 2px solid #000 !important;
        text-align: center !important;
        padding: 10px 0 !important;
    }
    
    /* Print-specific styles */
    body {
        font-size: 11pt !important;
        background: white !important;
        color: black !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }
    
    .table {
        font-size: 9pt !important;
        border-collapse: collapse !important;
        width: 100% !important;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .table td, .table th {
        border: 1px solid #dee2e6 !important;
        padding: 6px !important;
    }
    
    .fs-5 {
        font-size: 11pt !important;
    }
    
    /* Show signature section when printing */
    .signature-section {
        display: block !important;
        margin-top: 30px !important;
        page-break-inside: avoid !important;
    }
    
    /* Hide unnecessary elements */
    .alert, .btn-group, .d-flex .btn:not(.no-print),
    .card-header.bg-success, .card-header.bg-light,
    .row.mb-4 .card {
        display: none !important;
    }
    
    /* Show company header for print */
    .card-header:before {
        content: "TOKO ANDA";
        font-size: 18pt;
        font-weight: bold;
        display: block;
    }
    
    .card-header:after {
        content: "Jl. Contoh No. 123, Kota Anda | Telp: (021) 12345678";
        font-size: 9pt;
        display: block;
        margin-top: 5px;
    }
    
    .card-header h5 {
        font-size: 14pt !important;
        margin-top: 20px !important;
        margin-bottom: 0 !important;
    }
    
    /* Transaction info styling for print */
    .row.mb-4:first-of-type {
        display: flex !important;
        justify-content: space-between !important;
        margin-bottom: 15px !important;
        font-size: 9pt !important;
    }
    
    /* Badge styling for print */
    .badge {
        background-color: transparent !important;
        color: #000 !important;
        border: 1px solid #000 !important;
        padding: 2px 5px !important;
        font-size: 8pt !important;
    }
}

.badge {
    font-size: 0.75em;
}

.table-hover tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.05);
}

.table td, .table th {
    vertical-align: middle;
}

.alert-light {
    background-color: #f8f9fa;
    border-left: 4px solid #198754;
}

.signature-section {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>
@endpush