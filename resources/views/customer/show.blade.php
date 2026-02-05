@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->nama_customer)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Card Info Customer -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> Info Customer
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('customer.edit', $customer) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('customer.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-success bg-opacity-10 text-success rounded-circle fs-2">
                                {{ substr($customer->nama_customer, 0, 1) }}
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $customer->nama_customer }}</h4>
                        <span class="badge bg-success fs-6">{{ $customer->kode_customer }}</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="120"><i class="bi bi-telephone"></i> No. HP</th>
                                    <td>
                                        @if($customer->no_hp)
                                            <a href="tel:{{ $customer->no_hp }}" class="text-decoration-none">
                                                <i class="bi bi-telephone-outbound"></i> {{ $customer->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-geo-alt"></i> Alamat</th>
                                    <td>
                                        @if($customer->alamat)
                                            {{ $customer->alamat }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <td>
                                        @if($customer->email)
                                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                <i class="bi bi-envelope"></i> {{ $customer->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-calendar"></i> Dibuat</th>
                                    <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-arrow-clockwise"></i> Diperbarui</th>
                                    <td>{{ $customer->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Statistik -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-success mb-1">{{ $totalTransactions }}</h4>
                                <p class="mb-0 text-muted small">Total Transaksi</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-primary mb-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</h4>
                                <p class="mb-0 text-muted small">Total Pembelian</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-warning mb-1">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</h4>
                                <p class="mb-0 text-muted small">Rata-rata per Transaksi</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-danger mb-1">{{ $lastTransactionDate }}</h4>
                                <p class="mb-0 text-muted small">Transaksi Terakhir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Card Riwayat Transaksi -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check"></i> Riwayat Pembelian
                    </h5>
                    <span class="badge bg-light text-dark">{{ $barangKeluars->total() }} Transaksi</span>
                </div>
                <div class="card-body p-0">
                    @if($barangKeluars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Metode Bayar</th>
                                    <th class="text-center">Jumlah Item</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangKeluars as $barangKeluar)
                                <tr>
                                    <td>
                                        <strong>{{ $barangKeluar->no_transaksi }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Kasir: {{ $barangKeluar->user->name ?? '-' }}
                                        </small>
                                    </td>
                                    <td>{{ $barangKeluar->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        @if($barangKeluar->metode_pembayaran == 'cash')
                                            <span class="badge bg-success">Cash</span>
                                        @elseif($barangKeluar->metode_pembayaran == 'qris')
                                            <span class="badge bg-primary">QRIS</span>
                                        @elseif($barangKeluar->metode_pembayaran == 'transfer')
                                            <span class="badge bg-info">Transfer</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $barangKeluar->metode_pembayaran }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">
                                            {{ $barangKeluar->items->count() }} item
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($barangKeluar->items->sum('total_harga'), 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('barang-keluar.show', $barangKeluar) }}" 
                                               class="btn btn-info" 
                                               title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('barang-keluar.edit', $barangKeluar) }}" 
                                               class="btn btn-warning" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Detail items per transaksi -->
                                @if($barangKeluar->items->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="6" class="p-0">
                                        <div class="p-2 small">
                                            <strong>Items:</strong>
                                            @foreach($barangKeluar->items as $item)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $item->nama_barang_manual ?? $item->barang->nama_barang ?? 'Barang Manual' }} 
                                                ({{ number_format($item->jumlah, 2) }} {{ $item->unit_name }})
                                            </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($barangKeluars->hasPages())
                    <div class="card-footer">
                        {{ $barangKeluars->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x display-6 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum ada transaksi</h5>
                        <p class="text-muted small">Customer ini belum pernah melakukan pembelian</p>
                        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-cart-plus"></i> Buat Transaksi Baru
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Card Informasi Tambahan -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Catatan Customer</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Menampilkan riwayat transaksi penjualan ke customer ini.
                        Setiap transaksi dapat berisi beberapa item barang yang dijual.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .table-borderless td, .table-borderless th {
        border: none;
        padding: 8px 0;
    }
    .badge {
        font-size: 0.85em;
    }
</style>
@endpush