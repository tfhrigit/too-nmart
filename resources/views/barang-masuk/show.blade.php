@extends('layouts.app')

@section('title', 'Detail Barang Masuk: ' . $transaksi->no_transaksi)

@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Detail Barang Masuk: {{ $transaksi->no_transaksi }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Header Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Tanggal</th>
                                    <td>: {{ $transaksi->tanggal_formatted }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier</th>
                                    <td>: {{ $transaksi->supplier->nama_supplier }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Supplier</th>
                                    <td>: {{ $transaksi->supplier->kode_supplier }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>: {{ $transaksi->supplier->no_hp ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Invoice Supplier</th>
                                    <td>: {{ $transaksi->invoice_supplier ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Item</th>
                                    <td>: {{ $transaksi->items->count() }} barang</td>
                                </tr>
                                <tr>
                                    <th>Input Oleh</th>
                                    <td>: {{ $transaksi->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Input</th>
                                    <td>: {{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
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
                                    <th width="20%" class="text-end">Harga Beli</th>
                                    <th width="20%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ $item->barang->nama_barang ?? $item->nama_barang_manual }}
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
                                            <span class="badge bg-primary">
                                                {{ number_format($item->jumlah, 2) }} {{ $item->unit_name }}
                                            </span>
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
                                    <th colspan="4" class="text-end">Total Keseluruhan:</th>
                                    <th class="text-end text-success fs-5">
                                        Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Notes -->
                    @if($transaksi->keterangan)
                        <div class="alert alert-light mb-4">
                            <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Keterangan:</h6>
                            <p class="mb-0">{{ $transaksi->keterangan }}</p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                            </a>
                            <a href="{{ route('barang-masuk.print', $transaksi->no_transaksi) }}" target="_blank"
                                class="btn btn-outline-primary ms-2">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('barang-masuk.edit', $transaksi->no_transaksi) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('barang-masuk.destroy', $transaksi->no_transaksi) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus transaksi {{ $transaksi->no_transaksi }}? Stok akan dikembalikan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
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

            .btn,
            form,
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush