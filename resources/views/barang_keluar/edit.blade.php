@extends('layouts.app')

@section('title', 'Edit Barang Keluar')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Barang Keluar
                    <span class="badge bg-light text-dark ms-2">{{ $barangKeluar->no_transaksi }}</span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang-keluar.update', $barangKeluar) }}" method="POST" id="barangKeluarForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Header Info -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', $barangKeluar->tanggal->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">No. Transaksi</label>
                                <input type="text" class="form-control bg-light" value="{{ $barangKeluar->no_transaksi }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" id="metodePembayaran" class="form-control @error('metode_pembayaran') is-invalid @enderror" required>
                                    <option value="">Pilih metode...</option>
                                    <option value="cash" {{ old('metode_pembayaran', $barangKeluar->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="qris" {{ old('metode_pembayaran', $barangKeluar->metode_pembayaran) == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="transfer" {{ old('metode_pembayaran', $barangKeluar->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Section -->
                    <h6 class="border-bottom pb-2 mb-3">Pelanggan</h6>
                    <div class="mb-3">
                        <label class="form-label">Pilih Pelanggan</label>
                        <select name="customer_id" id="customerSelect" class="form-control select2-customer @error('customer_id') is-invalid @enderror">
                            <option value="">Pilih pelanggan...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $barangKeluar->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->nama_customer }}
                                    @if($customer->no_hp)
                                    ({{ $customer->no_hp }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan jika pelanggan umum</small>
                        @error('customer_id')
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
                                    <th width="20%">Harga Jual (Rp)</th>
                                    <th width="15%">Total (Rp)</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barangItems">
                                @php
                                    $oldItems = old('barang_items');
                                    $items = $oldItems ?? $barangKeluar->items;
                                    $itemCounter = 0;
                                @endphp
                                
                                @foreach($items as $index => $item)
                                @php
                                    $itemData = is_array($item) ? $item : $item->toArray();
                                    $mode = $itemData['barang_id'] ? 'existing' : 'manual';
                                @endphp
                                <tr class="barang-item" data-index="{{ $index }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="mb-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[{{ $index }}][mode]" 
                                                       value="existing" 
                                                       {{ $mode == 'existing' ? 'checked' : '' }}>
                                                <label class="form-check-label small">Pilih</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input barang-mode" type="radio" 
                                                       name="barang_items[{{ $index }}][mode]" 
                                                       value="manual"
                                                       {{ $mode == 'manual' ? 'checked' : '' }}>
                                                <label class="form-check-label small">Manual</label>
                                            </div>
                                        </div>
                                        
                                        <!-- Existing Barang Container -->
                                        <div class="existing-barang-container mb-2 {{ $mode == 'manual' ? 'd-none' : '' }}">
                                            <select name="barang_items[{{ $index }}][barang_id]" 
                                                    class="form-control select2-barang barang-select">
                                                @if($mode == 'existing' && $itemData['barang_id'])
                                                @php
                                                    $barang = is_array($itemData) ? 
                                                        App\Models\Barang::find($itemData['barang_id']) : 
                                                        $item->barang;
                                                @endphp
                                                <option value="{{ $itemData['barang_id'] }}" selected>
                                                    {{ $barang->nama_barang ?? 'Barang tidak ditemukan' }}
                                                </option>
                                                @endif
                                            </select>
                                        </div>
                                        
                                        <!-- Manual Barang Container -->
                                        <div class="manual-barang-container {{ $mode != 'manual' ? 'd-none' : '' }}">
                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <input type="text" 
                                                           name="barang_items[{{ $index }}][nama_manual]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Nama barang"
                                                           value="{{ $itemData['nama_barang_manual'] ?? '' }}">
                                                </div>
                                                <div class="col-5">
                                                    <select name="barang_items[{{ $index }}][satuan_manual]" 
                                                            class="form-control form-control-sm select2-satuan-sm">
                                                        <option value="">Satuan</option>
                                                        @foreach(['Kg', 'Pcs', 'Liter', 'Meter', 'Box', 'Sak', 'Dus', 'Karton'] as $unit)
                                                        <option value="{{ $unit }}" 
                                                                {{ ($itemData['unit_name'] ?? '') == $unit ? 'selected' : '' }}>
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
                                            @if($mode == 'existing' && isset($itemData['unit_name']))
                                            <option value="{{ $itemData['unit_name'] }}" selected>
                                                {{ $itemData['unit_name'] }}
                                            </option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" 
                                               name="barang_items[{{ $index }}][jumlah]" 
                                               class="form-control jumlah-input" 
                                               value="{{ $itemData['jumlah'] ?? '' }}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   name="barang_items[{{ $index }}][harga_jual]" 
                                                   class="form-control harga-input" 
                                                   value="{{ $itemData['harga_jual'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="total-display">
                                            @php
                                                $total = ($itemData['jumlah'] ?? 0) * ($itemData['harga_jual'] ?? 0);
                                            @endphp
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                        <input type="hidden" 
                                               name="barang_items[{{ $index }}][total]" 
                                               class="total-input" value="{{ $total }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item" 
                                                {{ $loop->first ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @php $itemCounter++; @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total Keseluruhan:</th>
                                    <th>
                                        <div id="grandTotalDisplay">
                                            Rp {{ number_format($barangKeluar->total_transaksi, 0, ',', '.') }}
                                        </div>
                                        <input type="hidden" name="grand_total" id="grandTotal" 
                                               value="{{ $barangKeluar->total_transaksi }}">
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $barangKeluar->keterangan) }}</textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('barang-keluar.show', $barangKeluar) }}" class="btn btn-secondary">
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

<!-- Modal Validasi -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Validasi Perubahan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-pencil-square text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-4">Periksa kembali data sebelum diperbarui</h5>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Perubahan data akan mempengaruhi stok barang!
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detail Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. Transaksi</strong></td>
                                <td>{{ $barangKeluar->no_transaksi }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td id="valTanggal">{{ $barangKeluar->tanggal->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pelanggan</strong></td>
                                <td id="valCustomer">{{ $barangKeluar->customer->nama_customer ?? 'Umum' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Metode Pembayaran</strong></td>
                                <td id="valMetode">{{ strtoupper($barangKeluar->metode_pembayaran) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detail Barang (Baru)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga Jual</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="valItems">
                                    <!-- Items will be populated by JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Transaksi:</th>
                                        <th id="valTotalHarga" class="fw-bold text-warning">Rp 0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left"></i> Perbaiki
                </button>
                <button type="button" class="btn btn-warning text-dark" id="confirmSubmit">
                    <i class="bi bi-check-lg"></i> Update Data
                </button>
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

.total-display {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    console.log('🚀 Edit Multi-Item Barang Keluar loaded');

    let itemCounter = {{ $itemCounter }};

    // Inisialisasi Customer Select2
    $('.select2-customer').select2({
        placeholder: 'Pilih pelanggan...',
        allowClear: true,
        width: '100%'
    });

    // Fungsi untuk menginisialisasi baris baru
    function initializeRow($row) {
        const index = $row.data('index');
        
        // Toggle barang mode
        $row.find('.barang-mode').change(function() {
            const mode = $(this).val();
            const $existing = $row.find('.existing-barang-container');
            const $manual = $row.find('.manual-barang-container');
            
            if (mode === 'existing') {
                $existing.removeClass('d-none');
                $manual.addClass('d-none');
                $row.find('.barang-select').prop('required', true);
                $row.find('[name*="[nama_manual]"]').prop('required', false);
            } else {
                $existing.addClass('d-none');
                $manual.removeClass('d-none');
                $row.find('.barang-select').prop('required', false);
                $row.find('[name*="[nama_manual]"]').prop('required', true);
                updateManualUnitSelect($row);
            }
        });
        
        // Barang Select2
        $row.find('.select2-barang').select2({
            placeholder: 'Pilih barang...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('barang-keluar.autocomplete-barang') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.results || []
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
        }).on('select2:select', function (e) {
            const data = e.params.data;
            updateUnitSelect($row, data);
            calculateRowTotal($row);
        });
        
        // Satuan Manual Select2
        $row.find('.select2-satuan-sm').select2({
            placeholder: 'Satuan',
            width: '100%',
            minimumResultsForSearch: 10
        }).on('select2:select', function () {
            updateManualUnitSelect($row);
            calculateRowTotal($row);
        });
        
        // Event untuk input jumlah dan harga
        $row.find('.jumlah-input, .harga-input').on('input', function () {
            calculateRowTotal($row);
            updateSummary();
        });
        
        // Event untuk remove item
        $row.find('.remove-item').on('click', function () {
            if ($('#barangItems tr').length > 1) {
                $row.remove();
                updateRowNumbers();
                updateSummary();
            }
        });
    }

    // Fungsi helper
    function updateUnitSelect($row, barangData) {
        const $unitSelect = $row.find('.unit-select');
        
        if (!barangData || !barangData.units) {
            $unitSelect.html('<option value="">Pilih...</option>');
            return;
        }
        
        let options = '<option value="">Pilih...</option>';
        const baseUnit = barangData.satuan || 'Unit';
        
        options += `<option value="${baseUnit}">${baseUnit} (Satuan Dasar)</option>`;
        
        barangData.units.forEach(unit => {
            if (unit.unit_name && unit.multiplier) {
                const text = unit.multiplier > 1 
                    ? `1 ${unit.unit_name} = ${unit.multiplier} ${baseUnit}`
                    : `1 ${baseUnit} = ${(1/unit.multiplier).toFixed(2)} ${unit.unit_name}`;
                    
                options += `<option value="${unit.unit_name}">${unit.unit_name} (${text})</option>`;
            }
        });
        
        $unitSelect.html(options);
    }
    
    function updateManualUnitSelect($row) {
        const unit = $row.find('select[name*="[satuan_manual]"]').val();
        if (unit) {
            $row.find('.unit-select')
                .html(`<option value="${unit}">${unit}</option>`)
                .val(unit)
                .trigger('change');
        }
    }
    
    function calculateRowTotal($row) {
        const jumlah = parseFloat($row.find('.jumlah-input').val()) || 0;
        const harga = parseFloat($row.find('.harga-input').val()) || 0;
        const total = jumlah * harga;
        
        $row.find('.total-display').text('Rp ' + total.toLocaleString('id-ID'));
        $row.find('.total-input').val(total);
        return total;
    }
    
    function updateSummary() {
        let total = 0;
        $('.barang-item').each(function () {
            total += calculateRowTotal($(this));
        });
        
        $('#grandTotalDisplay').text('Rp ' + total.toLocaleString('id-ID'));
        $('#grandTotal').val(total);
    }
    
    function updateRowNumbers() {
        $('.barang-item').each(function (i) {
            $(this).find('td:first').text(i + 1);
            updateRemoveButtons();
        });
    }
    
    function updateRemoveButtons() {
        $('.barang-item').each(function(index) {
            const $removeBtn = $(this).find('.remove-item');
            $removeBtn.prop('disabled', index === 0);
            if (index === 0) {
                $removeBtn.addClass('disabled');
            } else {
                $removeBtn.removeClass('disabled');
            }
        });
    }

    // Tambah baris baru
    $('#addBarangBtn').click(function() {
        const index = itemCounter++;
        const rowHtml = `
        <tr class="barang-item" data-index="${index}">
            <td class="text-center">${index + 1}</td>
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
                           name="barang_items[${index}][harga_jual]" 
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

    // Inisialisasi baris yang sudah ada
    $('.barang-item').each(function () {
        initializeRow($(this));
    });

    // Validasi form submit
    $('#barangKeluarForm').submit(function(e) {
        e.preventDefault();
        
        // Validasi minimal satu barang
        const itemCount = $('.barang-item').length;
        if (itemCount === 0) {
            Swal.fire({
                title: 'Validasi Gagal',
                text: 'Minimal satu barang harus ditambahkan',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
            return;
        }
        
        // Validasi setiap barang
        let isValid = true;
        let errorMessages = [];
        
        $('.barang-item').each(function(index) {
            const $row = $(this);
            const mode = $row.find('.barang-mode:checked').val();
            
            if (mode === 'existing') {
                const barangId = $row.find('.barang-select').val();
                if (!barangId) {
                    isValid = false;
                    errorMessages.push(`Barang ke-${index + 1}: Harus dipilih`);
                }
            } else {
                const namaManual = $row.find('[name*="[nama_manual]"]').val();
                if (!namaManual || namaManual.trim() === '') {
                    isValid = false;
                    errorMessages.push(`Barang ke-${index + 1}: Nama barang manual harus diisi`);
                }
            }
            
            const jumlah = $row.find('.jumlah-input').val();
            if (!jumlah || jumlah <= 0) {
                isValid = false;
                errorMessages.push(`Barang ke-${index + 1}: Jumlah harus lebih dari 0`);
            }
            
            const unit = $row.find('.unit-select').val();
            if (!unit) {
                isValid = false;
                errorMessages.push(`Barang ke-${index + 1}: Satuan harus dipilih`);
            }
            
            const harga = $row.find('.harga-input').val();
            if (!harga || harga <= 0) {
                isValid = false;
                errorMessages.push(`Barang ke-${index + 1}: Harga jual harus lebih dari 0`);
            }
        });
        
        if (!isValid) {
            Swal.fire({
                title: 'Validasi Gagal',
                html: '<div class="alert alert-danger text-start">' +
                      '<ul class="mb-0">' + 
                      errorMessages.map(msg => '<li>' + msg + '</li>').join('') +
                      '</ul></div>',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
            return;
        }
        
        // Tampilkan modal validasi
        const validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
        validationModal.show();
    });
    
    // Konfirmasi submit
    $('#confirmSubmit').click(function() {
        Swal.fire({
            title: 'Memperbarui Data...',
            text: 'Sedang menyimpan perubahan',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $('#barangKeluarForm').off('submit').submit();
    });

    updateRowNumbers();
    updateSummary();
});
</script>
@endpush