@extends('layouts.app')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box-arrow-in-down"></i> Tambah Barang Masuk (Multi Barang)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang-masuk.store') }}" method="POST" id="barangMasukForm">
                    @csrf
                    
                    <!-- Header Info -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">No. Transaksi</label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ 'BM-' . date('Ymd') . '-' . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Invoice Supplier</label>
                                <input type="text" name="invoice_supplier" class="form-control" 
                                       value="{{ old('invoice_supplier') }}" placeholder="No. Invoice/Faktur dari supplier">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Supplier Selection -->
                    <h6 class="border-bottom pb-2 mb-3">Supplier</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="supplier_mode" id="supplier_existing" 
                                   value="existing" {{ old('supplier_mode', 'existing') == 'existing' ? 'checked' : '' }}>
                            <label class="form-check-label" for="supplier_existing">
                                Pilih Supplier
                            </label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="supplier_mode" id="supplier_manual" 
                                   value="manual" {{ old('supplier_mode') == 'manual' ? 'checked' : '' }}>
                            <label class="form-check-label" for="supplier_manual">
                                Input Manual
                            </label>
                        </div>
                        
                        <div id="supplier_existing_container" class="{{ old('supplier_mode', 'existing') == 'existing' ? '' : 'd-none' }}">
                            <select name="supplier_id" id="supplierSelect" 
                                    class="form-control @error('supplier_id') is-invalid @enderror"
                                    {{ old('supplier_mode', 'existing') == 'existing' ? 'required' : '' }}>
                                @if(old('supplier_id') && old('supplier_mode') == 'existing')
                                    <option value="{{ old('supplier_id') }}" selected>
                                        {{ old('supplier_name') }}
                                    </option>
                                @endif
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="supplier_manual_container" class="{{ old('supplier_mode') == 'manual' ? '' : 'd-none' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                                    <input type="text" name="supplier_manual" class="form-control @error('supplier_manual') is-invalid @enderror" 
                                           value="{{ old('supplier_manual') }}" placeholder="Nama supplier baru">
                                    @error('supplier_manual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="supplier_telp" id="supplier_telp" 
                                           class="form-control" value="{{ old('supplier_telp') }}" 
                                           placeholder="0812-3456-7890">
                                    <div class="invalid-feedback phone-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barang Items Table -->
                    <h6 class="border-bottom pb-2 mb-3">
                        Daftar Barang
                        <button type="button" class="btn btn-sm btn-primary float-end" id="addBarangBtn">
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
                                <!-- Items will be added dynamically here -->
                                @if(old('barang_items'))
                                    @foreach(old('barang_items') as $index => $item)
                                    <tr class="barang-item" data-index="{{ $index }}">
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="mb-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input barang-mode" type="radio" 
                                                           name="barang_items[{{ $index }}][mode]" 
                                                           value="existing" 
                                                           {{ isset($item['mode']) && $item['mode'] == 'existing' ? 'checked' : 'checked' }}>
                                                    <label class="form-check-label small">Pilih</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input barang-mode" type="radio" 
                                                           name="barang_items[{{ $index }}][mode]" 
                                                           value="manual"
                                                           {{ isset($item['mode']) && $item['mode'] == 'manual' ? 'checked' : '' }}>
                                                    <label class="form-check-label small">Manual</label>
                                                </div>
                                            </div>
                                            
                                            <!-- Existing Barang Container -->
                                            <div class="existing-barang-container mb-2 {{ isset($item['mode']) && $item['mode'] == 'manual' ? 'd-none' : '' }}">
                                                <select name="barang_items[{{ $index }}][barang_id]" 
                                                        class="form-control select2-barang barang-select">
                                                    @if(isset($item['barang_id']) && $item['barang_id'])
                                                    <option value="{{ $item['barang_id'] }}" selected>
                                                        {{ $item['barang_name'] ?? '' }}
                                                    </option>
                                                    @endif
                                                </select>
                                            </div>
                                            
                                            <!-- Manual Barang Container -->
                                            <div class="manual-barang-container {{ (!isset($item['mode']) || $item['mode'] != 'manual') ? 'd-none' : '' }}">
                                                <div class="row g-2">
                                                    <div class="col-7">
                                                        <input type="text" 
                                                               name="barang_items[{{ $index }}][nama_manual]" 
                                                               class="form-control form-control-sm" 
                                                               placeholder="Nama barang"
                                                               value="{{ $item['nama_manual'] ?? '' }}">
                                                    </div>
                                                    <div class="col-5">
                                                        <select name="barang_items[{{ $index }}][satuan_manual]" 
                                                                class="form-control form-control-sm select2-satuan-sm">
                                                            <option value="">Satuan</option>
                                                            @foreach(['Kg', 'Pcs', 'Liter', 'Meter', 'Box', 'Sak', 'Dus', 'Karton'] as $unit)
                                                            <option value="{{ $unit }}" 
                                                                    {{ ($item['satuan_manual'] ?? '') == $unit ? 'selected' : '' }}>
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
                                                <option value="">Pilih...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" 
                                                   name="barang_items[{{ $index }}][jumlah]" 
                                                   class="form-control jumlah-input" 
                                                   value="{{ $item['jumlah'] ?? '' }}">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" 
                                                       name="barang_items[{{ $index }}][harga_beli]" 
                                                       class="form-control harga-input" 
                                                       value="{{ $item['harga_beli'] ?? '' }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="total-display">Rp 0</div>
                                            <input type="hidden" 
                                                   name="barang_items[{{ $index }}][total]" 
                                                   class="total-input" value="0">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" 
                                                    {{ $loop->first ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <!-- Default first row -->
                                <tr class="barang-item" data-index="0">
                                    <td class="text-center">1</td>
                                    <td>
                                        <div class="mb-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[0][mode]" value="existing" checked>
                                                <label class="form-check-label small">Pilih</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[0][mode]" value="manual">
                                                <label class="form-check-label small">Manual</label>
                                            </div>
                                        </div>
                                        
                                        <!-- Existing Barang Container -->
                                        <div class="existing-barang-container mb-2">
                                            <select name="barang_items[0][barang_id]" 
                                                    class="form-control select2-barang barang-select">
                                            </select>
                                        </div>
                                        
                                        <!-- Manual Barang Container -->
                                        <div class="manual-barang-container d-none">
                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <input type="text" 
                                                           name="barang_items[0][nama_manual]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Nama barang">
                                                </div>
                                                <div class="col-5">
                                                    <select name="barang_items[0][satuan_manual]" 
                                                            class="form-control form-control-sm select2-satuan-sm">
                                                        <option value="">Satuan</option>
                                                        @foreach(['Kg', 'Pcs', 'Liter', 'Meter', 'Box', 'Sak', 'Dus', 'Karton'] as $unit)
                                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="barang_items[0][unit_name]" 
                                                class="form-control unit-select">
                                            <option value="">Pilih...</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" 
                                               name="barang_items[0][jumlah]" 
                                               class="form-control jumlah-input">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   name="barang_items[0][harga_beli]" 
                                                   class="form-control harga-input">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="total-display">Rp 0</div>
                                        <input type="hidden" 
                                               name="barang_items[0][total]" 
                                               class="total-input" value="0">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total Keseluruhan:</th>
                                    <th>
                                        <div id="grandTotalDisplay">Rp 0</div>
                                        <input type="hidden" name="grand_total" id="grandTotal" value="0">
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
                                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Ringkasan</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jumlah Item:</span>
                                        <strong id="itemCount">0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Total Nilai:</span>
                                        <strong id="summaryTotal">Rp 0</strong>
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
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save"></i> Simpan Transaksi
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

.select2-satuan-sm + .select2-container {
    min-width: 80px;
}

.manual-barang-container .select2-container {
    width: 100% !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('🚀 Multi-Item Barang Masuk loaded');
    
    // CSRF Token Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    let itemCounter = {{ old('barang_items') ? count(old('barang_items')) : 1 }};
    
    // ==================== SUPPLIER SELECT2 ====================
    $('#supplierSelect').select2({
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
    
    // ==================== TOGGLE SUPPLIER MODE ====================
    $('input[name="supplier_mode"]').change(function() {
        const mode = $(this).val();
        if (mode === 'existing') {
            $('#supplier_existing_container').removeClass('d-none');
            $('#supplier_manual_container').addClass('d-none');
            $('#supplierSelect').prop('required', true);
        } else {
            $('#supplier_existing_container').addClass('d-none');
            $('#supplier_manual_container').removeClass('d-none');
            $('#supplierSelect').prop('required', false);
        }
    });
    
    // ==================== ADD NEW BARANG ROW ====================
    $('#addBarangBtn').click(function() {
        const index = itemCounter++;
        const rowHtml = `
        <tr class="barang-item" data-index="${index}">
            <td class="text-center">${itemCounter}</td>
            <td>
                <div class="mb-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input barang-mode" type="radio" 
                               name="barang_items[${index}][mode]" value="existing" checked>
                        <label class="form-check-label small">Pilih</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input barang-mode" type="radio" 
                               name="barang_items[${index}][mode]" value="manual">
                        <label class="form-check-label small">Manual</label>
                    </div>
                </div>
                
                <div class="existing-barang-container mb-2">
                    <select name="barang_items[${index}][barang_id]" 
                            class="form-control select2-barang barang-select">
                    </select>
                </div>
                
                <div class="manual-barang-container d-none">
                    <div class="row g-2">
                        <div class="col-7">
                            <input type="text" 
                                   name="barang_items[${index}][nama_manual]" 
                                   class="form-control form-control-sm" 
                                   placeholder="Nama barang">
                        </div>
                        <div class="col-5">
                            <select name="barang_items[${index}][satuan_manual]" 
                                    class="form-control form-control-sm select2-satuan-sm">
                                <option value="">Satuan</option>
                                @foreach(['Kg', 'Pcs', 'Liter', 'Meter', 'Box', 'Sak', 'Dus', 'Karton'] as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <select name="barang_items[${index}][unit_name]" 
                        class="form-control unit-select">
                    <option value="">Pilih...</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" 
                       name="barang_items[${index}][jumlah]" 
                       class="form-control jumlah-input">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" 
                           name="barang_items[${index}][harga_beli]" 
                           class="form-control harga-input">
                </div>
            </td>
            <td>
                <div class="total-display">Rp 0</div>
                <input type="hidden" 
                       name="barang_items[${index}][total]" 
                       class="total-input" value="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#barangItems').append(rowHtml);
        initializeRow($('#barangItems tr:last'));
        updateRowNumbers();
        updateSummary();
    });
    
    // ==================== INITIALIZE ROW ====================
    function initializeRow($row) {
        const index = $row.data('index');
        
        // Initialize Select2 for barang selection
        $row.find('.select2-barang').select2({
            placeholder: 'Pilih barang...',
            allowClear: true,
            ajax: {
                url: "{{ route('barang-masuk.autocomplete-barang') }}",
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
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                
                const stokStatus = data.stok_status || 'bg-secondary';
                const stokText = data.stok <= 0 ? 'Habis' : 
                                data.stok <= (data.stok_minimum || 10) ? 'Kritis' : 'Aman';
                
                return $(`
                    <div class="d-flex justify-content-between align-items-center p-1">
                        <div>
                            <strong>${data.nama || data.text}</strong>
                            <div class="small text-muted">${data.kode || ''}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge ${stokStatus}">${stokText}</span>
                            <div class="small">${data.stok || 0} ${data.satuan || ''}</div>
                        </div>
                    </div>
                `);
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                return data.nama || data.text;
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;

            // Auto-fill harga_beli jika tersedia (nilai > 0)
            if (data.harga_beli && parseFloat(data.harga_beli) > 0) {
                $row.find('.harga-input').val(data.harga_beli).trigger('input');
            } else {
                $row.find('.harga-input').val('').trigger('input');
            }

            updateUnitSelect($row, data);
        });
        
        // Initialize Select2 for manual unit
        $row.find('.select2-satuan-sm').select2({
            placeholder: 'Satuan',
            width: '100%',
            minimumResultsForSearch: 10
        }).on('select2:select', function() {
            updateManualUnitSelect($row);
        });
        
        // Toggle mode change
        $row.find('.barang-mode').change(function() {
            const mode = $(this).val();
            const $container = $row.find('.existing-barang-container');
            const $manualContainer = $row.find('.manual-barang-container');
            
            if (mode === 'existing') {
                $container.removeClass('d-none');
                $manualContainer.addClass('d-none');
                $row.find('.barang-select').prop('required', true);
                $row.find('input[name*="[nama_manual]"]').prop('required', false);
                $row.find('select[name*="[satuan_manual]"]').prop('required', false);
            } else {
                $container.addClass('d-none');
                $manualContainer.removeClass('d-none');
                $row.find('.barang-select').prop('required', false);
                $row.find('input[name*="[nama_manual]"]').prop('required', true);
                $row.find('select[name*="[satuan_manual]"]').prop('required', true);
                updateManualUnitSelect($row);
            }
        });
        
        // Calculate totals on input
        $row.find('.jumlah-input, .harga-input').on('input', function() {
            calculateRowTotal($row);
            updateSummary();
        });
        
        // Remove row
        $row.find('.remove-item').click(function() {
            if ($('#barangItems tr').length > 1) {
                $row.remove();
                updateRowNumbers();
                updateSummary();
            }
        });
    }
    
    // ==================== UPDATE UNIT SELECT ====================
    function updateUnitSelect($row, barangData) {
        const $unitSelect = $row.find('.unit-select');
        
        if (!barangData || !barangData.units) {
            $unitSelect.html('<option value="">Pilih...</option>');
            return;
        }
        
        let options = '<option value="">Pilih...</option>';
        const baseUnit = barangData.satuan || 'Unit';
        
        // Add base unit
        options += `<option value="${baseUnit}">${baseUnit} (Satuan Dasar)</option>`;
        
        // Add other units
        if (Array.isArray(barangData.units)) {
            barangData.units.forEach(function(unit) {
                if (!unit.is_base && unit.unit_name) {
                    const multiplierText = unit.multiplier > 1 ? 
                        `1 ${unit.unit_name} = ${unit.multiplier} ${baseUnit}` :
                        `1 ${baseUnit} = ${(1/unit.multiplier).toFixed(2)} ${unit.unit_name}`;
                    
                    options += `<option value="${unit.unit_name}">${unit.unit_name} (${multiplierText})</option>`;
                }
            });
        }
        
        $unitSelect.html(options).val(baseUnit).trigger('change');
    }
    
    function updateManualUnitSelect($row) {
        const manualUnit = $row.find('select[name*="[satuan_manual]"]').val();
        const $unitSelect = $row.find('.unit-select');
        
        if (manualUnit) {
            $unitSelect.html(`<option value="${manualUnit}">${manualUnit}</option>`)
                       .val(manualUnit).trigger('change');
        }
    }
    
    // ==================== CALCULATIONS ====================
    function calculateRowTotal($row) {
        const jumlah = parseFloat($row.find('.jumlah-input').val()) || 0;
        const harga = parseFloat($row.find('.harga-input').val()) || 0;
        const total = jumlah * harga;
        
        $row.find('.total-display').text('Rp ' + total.toLocaleString('id-ID'));
        $row.find('.total-input').val(total);
        
        return total;
    }
    
    function updateSummary() {
        let grandTotal = 0;
        let itemCount = 0;
        
        $('.barang-item').each(function() {
            const total = calculateRowTotal($(this));
            if (!isNaN(total) && total > 0) {
                grandTotal += total;
                itemCount++;
            }
        });
        
        $('#grandTotalDisplay').text('Rp ' + grandTotal.toLocaleString('id-ID'));
        $('#grandTotal').val(grandTotal);
        $('#itemCount').text(itemCount);
        $('#summaryTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    }
    
    function updateRowNumbers() {
        $('.barang-item').each(function(index) {
            $(this).find('td:first').text(index + 1);
            // Enable/disable remove button
            $(this).find('.remove-item').prop('disabled', index === 0);
        });
    }
    
    // ==================== FORM VALIDATION ====================
    $('#barangMasukForm').submit(function(e) {
        e.preventDefault();
        
        let isValid = true;
        let errorMessages = [];
        
        // Validate supplier
        const supplierMode = $('input[name="supplier_mode"]:checked').val();
        if (supplierMode === 'existing') {
            if (!$('#supplierSelect').val()) {
                isValid = false;
                errorMessages.push('Supplier harus dipilih');
            }
        } else {
            const supplierName = $('input[name="supplier_manual"]').val();
            if (!supplierName || supplierName.trim() === '') {
                isValid = false;
                errorMessages.push('Nama supplier harus diisi');
            }
        }
        
        // Validate at least one item
        if ($('.barang-item').length === 0) {
            isValid = false;
            errorMessages.push('Minimal satu barang harus ditambahkan');
        }
        
        // Validate each item
        $('.barang-item').each(function(index) {
            const $row = $(this);
            const mode = $row.find('input[name*="[mode]"]:checked').val();
            const rowNum = index + 1;
            
            if (mode === 'existing') {
                const barangId = $row.find('.barang-select').val();
                if (!barangId) {
                    isValid = false;
                    errorMessages.push(`Barang #${rowNum}: Pilih barang yang sudah ada`);
                }
            } else {
                const namaManual = $row.find('input[name*="[nama_manual]"]').val();
                const satuanManual = $row.find('select[name*="[satuan_manual]"]').val();
                
                if (!namaManual || namaManual.trim() === '') {
                    isValid = false;
                    errorMessages.push(`Barang #${rowNum}: Nama barang manual harus diisi`);
                }
                if (!satuanManual) {
                    isValid = false;
                    errorMessages.push(`Barang #${rowNum}: Satuan barang manual harus dipilih`);
                }
            }
            
            const unit = $row.find('.unit-select').val();
            if (!unit) {
                isValid = false;
                errorMessages.push(`Barang #${rowNum}: Satuan harus dipilih`);
            }
            
            const jumlah = parseFloat($row.find('.jumlah-input').val());
            if (!jumlah || jumlah <= 0) {
                isValid = false;
                errorMessages.push(`Barang #${rowNum}: Jumlah harus lebih dari 0`);
            }
            
            const harga = parseFloat($row.find('.harga-input').val());
            if (!harga || harga <= 0) {
                isValid = false;
                errorMessages.push(`Barang #${rowNum}: Harga beli harus lebih dari 0`);
            }
        });
        
        if (!isValid) {
            Swal.fire({
                title: 'Validasi Gagal',
                html: '<div class="alert alert-danger text-start"><ul class="mb-0">' + 
                      errorMessages.map(msg => '<li>' + msg + '</li>').join('') + '</ul></div>',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
            return;
        }
        
        // Submit form
        $(this).off('submit').submit();
    });
    
    // ==================== INITIALIZE EXISTING ROWS ====================
    $('.barang-item').each(function() {
        initializeRow($(this));
    });
    
    updateSummary();
    updateRowNumbers();
    
    console.log('✅ Multi-Item Barang Masuk initialized successfully!');
});
</script>
@endpush
