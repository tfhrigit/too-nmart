@extends('layouts.app')

@section('title', 'Edit Barang Masuk: ' . $transaksi->no_transaksi)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Barang Masuk: {{ $transaksi->no_transaksi }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang-masuk.update', $transaksi->no_transaksi) }}" method="POST" id="barangMasukForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Header Info -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">No. Transaksi</label>
                                <input type="text" class="form-control bg-light" value="{{ $transaksi->no_transaksi }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Invoice Supplier</label>
                                <input type="text" name="invoice_supplier" class="form-control" 
                                       value="{{ old('invoice_supplier', $transaksi->invoice_supplier) }}" placeholder="No. Invoice/Faktur">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Supplier Selection -->
                    <h6 class="border-bottom pb-2 mb-3">Supplier</h6>
                    <div class="mb-3">
                        <label class="form-label">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplierSelect" 
                                class="form-control select2-supplier @error('supplier_id') is-invalid @enderror" required>
                            @if($transaksi->supplier)
                                <option value="{{ $transaksi->supplier->id }}" selected>
                                    {{ $transaksi->supplier->nama_supplier }} ({{ $transaksi->supplier->kode_supplier }})
                                </option>
                            @endif
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Barang Items Table -->
                    <h6 class="border-bottom pb-2 mb-3">
                        Daftar Barang
                        <button type="button" class="btn btn-sm btn-warning float-end" id="addBarangBtn">
                            <i class="bi bi-plus-circle"></i> Tambah Barang
                        </button>
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="barangTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Barang</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="20%">Harga Beli (Rp)</th>
                                    <th width="15%">Total (Rp)</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barangItems">
                                @foreach($transaksi->items as $index => $item)
                                <tr class="barang-item" data-index="{{ $index }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="mb-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[{{ $index }}][mode]" value="existing" checked>
                                                <label class="form-check-label small">Pilih</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[{{ $index }}][mode]" value="manual">
                                                <label class="form-check-label small">Manual</label>
                                            </div>
                                        </div>
                                        
                                        <!-- Existing Barang Container -->
                                        <div class="existing-barang-container mb-2">
                                            <select name="barang_items[{{ $index }}][barang_id]" 
                                                    class="form-control select2-barang barang-select">
                                                @if($item->barang)
                                                <option value="{{ $item->barang->id }}" selected>
                                                    {{ $item->barang->nama_barang }} ({{ $item->barang->kode_barang }})
                                                </option>
                                                @endif
                                            </select>
                                        </div>
                                        
                                        <!-- Manual Barang Container (Hidden) -->
                                        <div class="manual-barang-container d-none">
                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <input type="text" 
                                                           name="barang_items[{{ $index }}][nama_manual]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Nama barang"
                                                           value="{{ $item->nama_barang_manual ?? '' }}">
                                                </div>
                                                <div class="col-5">
                                                    <select name="barang_items[{{ $index }}][satuan_manual]" 
                                                            class="form-control form-control-sm select2-satuan-sm">
                                                        <option value="">Satuan</option>
                                                        @foreach(['Kg', 'Pcs', 'Liter', 'Meter', 'Box', 'Sak', 'Dus', 'Karton'] as $unit)
                                                        <option value="{{ $unit }}" 
                                                                {{ ($item->unit_name ?? '') == $unit ? 'selected' : '' }}>
                                                            {{ $unit }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="barang_items[{{ $index }}][unit_name]" 
                                                class="form-control unit-select">
                                            <option value="{{ $item->unit_name }}" selected>{{ $item->unit_name }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" 
                                               name="barang_items[{{ $index }}][jumlah]" 
                                               class="form-control jumlah-input" 
                                               value="{{ old('barang_items.' . $index . '.jumlah', $item->jumlah) }}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   name="barang_items[{{ $index }}][harga_beli]" 
                                                   class="form-control harga-input" 
                                                   value="{{ old('barang_items.' . $index . '.harga_beli', $item->harga_beli) }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="total-display">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</div>
                                        <input type="hidden" 
                                               name="barang_items[{{ $index }}][total]" 
                                               class="total-input" value="{{ $item->total_harga }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item" 
                                                {{ $loop->first ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total Keseluruhan:</th>
                                    <th>
                                        <div id="grandTotalDisplay">Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</div>
                                        <input type="hidden" name="grand_total" id="grandTotal" value="{{ $transaksi->grand_total }}">
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Summary & Notes -->
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h6 class="card-title">Ringkasan</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jumlah Item:</span>
                                        <strong id="itemCount">{{ $transaksi->items->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Total Nilai:</span>
                                        <strong id="summaryTotal">Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning text-dark" id="submitBtn">
                            <i class="bi bi-save"></i> Update Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border-radius: 0.375rem;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 12px;
    line-height: 36px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.barang-item {
    transition: background-color 0.2s;
}

.barang-item:hover {
    background-color: #f8f9fa;
}

.total-display {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    font-weight: 600;
}

.remove-item:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('🚀 Edit Barang Masuk loaded');
    
    let itemCounter = {{ $transaksi->items->count() }};
    
    // ==================== SUPPLIER SELECT2 ====================
    $('.select2-supplier').select2({
        placeholder: 'Pilih supplier...',
        allowClear: true,
        ajax: {
            url: "{{ route('barang-masuk.autocomplete-supplier') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term || '' };
            },
            processResults: function(response) {
                return {
                    results: response.results || []
                };
            }
        }
    });
    
    // ==================== INITIALIZE EXISTING ROWS ====================
    $('.barang-item').each(function() {
        initializeRow($(this));
        calculateRowTotal($(this));
    });
    
    updateSummary();
    
    // ... (kode JavaScript lainnya sama seperti di create.blade.php)
    // Copy semua JavaScript dari create.blade.php dan paste di sini
    
    console.log('✅ Edit Barang Masuk initialized successfully!');
});
</script>
@endpush