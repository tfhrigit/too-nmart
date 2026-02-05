@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <!-- Info Barang -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Detail Barang</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Kode Barang</th>
                                <td>: <strong>{{ $barang->kode_barang }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>: <strong>{{ $barang->nama_barang }}</strong></td>
                            </tr>
                            <tr>
                                <th>Satuan Dasar</th>
                                <td>: <span class="badge bg-primary">{{ $barang->base_unit }}</span></td>
                            </tr>
                            <tr>
                                <th>Stok Sekarang</th>
                                <td>: 
                                    <strong class="fs-5 {{ $barang->isStokKritis() ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($barang->stok_sekarang, 2) }} {{ $barang->base_unit }}
                                    </strong>
                                    @if($barang->isStokHabis())
                                        <span class="badge bg-danger">Habis</span>
                                    @elseif($barang->isStokKritis())
                                        <span class="badge bg-warning">Kritis</span>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Harga Beli</th>
                                <td>: 
                                    @if($barang->harga_beli && $barang->harga_beli > 0)
                                        <strong>Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Harga Jual</th>
                                <td>: 
                                    @if($barang->harga_jual && $barang->harga_jual > 0)
                                        <strong>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Stok Minimum</th>
                                <td>: {{ $barang->stok_minimum }} {{ $barang->base_unit }}</td>
                            </tr>
                            <tr>
                                <th>Total Masuk</th>
                                <td>: {{ number_format($barang->total_barang_masuk, 2) }} {{ $barang->base_unit }}</td>
                            </tr>
                            <tr>
                                <th>Total Keluar</th>
                                <td>: {{ number_format($barang->total_barang_keluar, 2) }} {{ $barang->base_unit }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>: {{ $barang->deskripsi ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('barang.edit', $barang) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Riwayat Transaksi -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-box-arrow-in-down"></i> Barang Masuk Terakhir</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Transaksi</th>
                                        <th>Supplier</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($barang->barangMasuks->take(5) as $masuk)
                                    <tr>
                                        <td>{{ $masuk->tanggal->format('d/m/Y') }}</td>
                                        <td><small>{{ $masuk->no_transaksi }}</small></td>
                                        <td>{{ $masuk->supplier->nama_supplier ?? '-' }}</td>
                                        <td>{{ number_format($masuk->jumlah, 2) }} {{ $masuk->unit_name }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada transaksi masuk</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-box-arrow-up"></i> Barang Keluar Terakhir</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Transaksi</th>
                                        <th>Customer</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($barang->barangKeluarItems->take(5) as $item)
                                    <tr>
                                        <td>{{ $item->barangKeluar->tanggal->format('d/m/Y') ?? '-' }}</td>
                                        <td><small>{{ $item->barangKeluar->no_transaksi ?? '-' }}</small></td>
                                        <td>{{ $item->barangKeluar->customer->nama_customer ?? 'Umum' }}</td>
                                        <td>{{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada transaksi keluar</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock History -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Stok</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Transaksi</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang->stockHistories->take(10) as $history)
                            <tr>
                                <td>{{ $history->tanggal->format('d/m/Y') }}</td>
                                <td><small>{{ $history->referensi_tabel }}</small></td>
                                <td>
                                    @if($history->jenis_transaksi == 'masuk')
                                        <span class="badge bg-success">Masuk</span>
                                    @else
                                        <span class="badge bg-danger">Keluar</span>
                                    @endif
                                </td>
                                <td>{{ number_format($history->jumlah, 2) }} {{ $barang->base_unit }}</td>
                                <td>{{ number_format($history->stok_sebelum, 2) }}</td>
                                <td>{{ number_format($history->stok_sesudah, 2) }}</td>
                                <td><small>{{ $history->keterangan }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada riwayat stok</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection