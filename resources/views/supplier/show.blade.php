@extends('layouts.app')

@section('title', 'Detail Supplier - ' . $supplier->nama_supplier)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Card Info Supplier (sama seperti sebelumnya) -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-truck"></i> Info Supplier
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle fs-2">
                                {{ substr($supplier->nama_supplier, 0, 1) }}
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $supplier->nama_supplier }}</h4>
                        <span class="badge bg-primary fs-6">{{ $supplier->kode_supplier }}</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="120"><i class="bi bi-geo-alt"></i> Alamat</th>
                                    <td>
                                        @if($supplier->alamat)
                                            {{ $supplier->alamat }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-telephone"></i> No. HP</th>
                                    <td>
                                        @if($supplier->no_hp)
                                            <a href="tel:{{ $supplier->no_hp }}" class="text-decoration-none">
                                                {{ $supplier->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <td>
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                                {{ $supplier->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-calendar"></i> Dibuat</th>
                                    <td>{{ $supplier->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-arrow-clockwise"></i> Diperbarui</th>
                                    <td>{{ $supplier->updated_at->format('d/m/Y H:i') }}</td>
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
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-primary mb-1">{{ $totalTransactions }}</h4>
                                <p class="mb-0 text-muted small">Total Transaksi</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <h4 class="text-success mb-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</h4>
                                <p class="mb-0 text-muted small">Total Nilai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Card Riwayat Transaksi -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Riwayat Barang Masuk
                    </h5>
                    <span class="badge bg-light text-dark">{{ $transactions->count() }} Transaksi</span>
                </div>

                <!-- Search Filter -->
                <div class="card-body pb-2">
                    <form method="GET" action="{{ route('supplier.show', $supplier->id) }}" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Cari no transaksi, invoice, atau nama barang..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="tanggal_mulai" class="form-control" 
                                placeholder="Dari tanggal"
                                value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" name="tanggal_akhir" class="form-control" 
                                    placeholder="Sampai tanggal"
                                    value="{{ request('tanggal_akhir') }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                                <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Invoice Supplier</th>
                                    <th class="text-center">Jumlah Item</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>{{ $transaction->no_transaksi }}</strong>
                                    </td>
                                    <td>{{ $transaction->tanggal_formatted }}</td>
                                    <td>
                                        @if($transaction->invoice_supplier)
                                            <span class="badge bg-info">{{ $transaction->invoice_supplier }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">
                                            {{ $transaction->items->count() }} item
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('barang-masuk.show', $transaction->no_transaksi) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Detail items per transaksi (opsional) -->
                                @if($transaction->items->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="6" class="p-0">
                                        <div class="p-2 small">
                                            <strong>Items:</strong>
                                            @foreach($transaction->items as $item)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $item->barang->nama_barang }} 
                                                ({{ $item->jumlah }} {{ $item->unit_name }})
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
                    
                    @if($transactions->hasPages())
                    <div class="card-footer">
                        {{ $transactions->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        @if(request('search') || request('tanggal_mulai') || request('tanggal_akhir'))
                            <i class="bi bi-search display-6 text-warning"></i>
                            <h5 class="mt-3 text-muted">Tidak ada hasil pencarian</h5>
                            <p class="text-muted small">Coba gunakan kata kunci yang berbeda</p>
                        @else
                            <i class="bi bi-box-arrow-in-down display-6 text-muted"></i>
                            <h5 class="mt-3 text-muted">Belum ada transaksi</h5>
                            <p class="text-muted small">Supplier ini belum pernah melakukan transaksi barang masuk</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Card Informasi Tambahan -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Catatan</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Menampilkan riwayat transaksi barang masuk dari supplier ini.
                        Setiap transaksi dapat berisi beberapa item barang.
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