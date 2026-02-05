@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $noTransaksi)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt"></i> Detail Transaksi: {{ $noTransaksi }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Transaction Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Tanggal</th>
                                <td>: {{ $items->first()->tanggal->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>: {{ $items->first()->supplier->nama_supplier }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>: {{ $items->first()->supplier->no_hp ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Invoice Supplier</th>
                                <td>: {{ $items->first()->invoice_supplier ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Input Oleh</th>
                                <td>: {{ $items->first()->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Input</th>
                                <td>: {{ $items->first()->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->barang->nama_barang }}</div>
                                    <div class="small text-muted">
                                        Kode: {{ $item->barang->kode_barang }}
                                        | Stok Sekarang: {{ $item->barang->stok_sekarang }} {{ $item->barang->base_unit }}
                                    </div>
                                </td>
                                <td class="text-center">
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
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total {{ $totalItems }} Item:</th>
                                <th class="text-end">Rp {{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Notes -->
                @if($items->first()->keterangan)
                <div class="mt-3">
                    <h6>Keterangan:</h6>
                    <div class="alert alert-light">
                        {{ $items->first()->keterangan }}
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div class="btn-group">
                        <a href="{{ route('barang-masuk.edit', $items->first()) }}" 
                           class="btn btn-warning {{ $items->count() > 1 ? 'disabled' : '' }}"
                           title="{{ $items->count() > 1 ? 'Edit tidak tersedia untuk multi-item' : 'Edit' }}">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('barang-masuk.destroy-transaksi', $noTransaksi) }}" 
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus transaksi {{ $noTransaksi }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus Transaksi
                            </button>
                        </form>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
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
    .btn, form, .card-header {
        display: none !important;
    }
}
</style>
@endpush