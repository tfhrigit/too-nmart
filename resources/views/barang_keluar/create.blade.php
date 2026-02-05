@extends('layouts.app')

@section('title', 'Input Barang Keluar')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-box-arrow-up"></i> Input Barang Keluar (Multi Barang)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang-keluar.store') }}" method="POST" id="barangKeluarForm">
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
                                       value="{{ 'BK-' . date('Ymd') . '-' . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" id="metodePembayaran" class="form-control @error('metode_pembayaran') is-invalid @enderror" required>
                                    <option value="">Pilih metode...</option>
                                    <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
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
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="customer_mode" id="customer_existing" 
                                   value="existing" {{ old('customer_mode', 'existing') == 'existing' ? 'checked' : '' }}>
                            <label class="form-check-label" for="customer_existing">
                                Pilih Pelanggan
                            </label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="radio" name="customer_mode" id="customer_manual" 
                                   value="manual" {{ old('customer_mode') == 'manual' ? 'checked' : '' }}>
                            <label class="form-check-label" for="customer_manual">
                                Input Manual
                            </label>
                        </div>
                        
                        <div id="customer_existing_container" class="{{ old('customer_mode', 'existing') == 'existing' ? '' : 'd-none' }}">
                            <select name="customer_id" id="customerSelect" 
                                    class="form-control @error('customer_id') is-invalid @enderror"
                                    {{ old('customer_mode', 'existing') == 'existing' ? '' : 'disabled' }}>
                                @if(old('customer_id') && old('customer_mode') == 'existing')
                                    <option value="{{ old('customer_id') }}" selected>
                                        {{ old('customer_name') }}
                                    </option>
                                @endif
                            </select>
                            <small class="text-muted">Kosongkan jika pelanggan umum</small>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="customer_manual_container" class="{{ old('customer_mode') == 'manual' ? '' : 'd-none' }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" name="customer_manual" id="customerManualInput"
                                           class="form-control @error('customer_manual') is-invalid @enderror"
                                           value="{{ old('customer_manual') }}" placeholder="Nama pelanggan baru"
                                           {{ old('customer_mode') == 'manual' ? '' : 'disabled' }}>
                                    @error('customer_manual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. HP</label>
                                    <input type="tel" name="customer_hp" id="customer_hp" 
                                           class="form-control @error('customer_hp') is-invalid @enderror" 
                                           value="{{ old('customer_hp') }}" placeholder="0812-3456-7890" maxlength="15"
                                           {{ old('customer_mode') == 'manual' ? '' : 'disabled' }}>
                                    <div class="invalid-feedback phone-error"></div>
                                    <small class="text-muted">Format: 0812-3456-7890 (otomatis)</small>
                                    @error('customer_hp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barang Items Table -->
                    <h6 class="border-bottom pb-2 mb-3">
                        Daftar Barang
                        <button type="button" class="btn btn-sm btn-success float-end" id="addBarangBtn">
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
                                                       name="barang_items[{{ $index }}][harga_jual]" 
                                                       class="form-control harga-input" 
                                                       value="{{ $item['harga_jual'] ?? '' }}">
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
                                                   name="barang_items[0][harga_jual]" 
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
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="bi bi-save"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>

                <!-- Modal Validasi -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="bi bi-check-circle"></i> Validasi Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-clipboard-check text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-4">Periksa kembali data sebelum disimpan</h5>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detail Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Tanggal</strong></td>
                                <td id="valTanggal">-</td>
                            </tr>
                            <tr>
                                <td><strong>Pelanggan</strong></td>
                                <td id="valCustomer">Umum</td>
                            </tr>
                            <tr>
                                <td><strong>Metode Pembayaran</strong></td>
                                <td id="valMetode">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detail Barang</h6>
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
                                        <th id="valTotalHarga" class="fw-bold text-success">Rp 0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Pastikan stok barang mencukupi dan data sudah benar.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left"></i> Perbaiki
                </button>
                <button type="button" class="btn btn-success" id="confirmSubmit">
                    <i class="bi bi-check-lg"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

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

/* Fix untuk dropdown select2 yang tidak muncul */
.select2-container--open .select2-dropdown--below {
    z-index: 1060 !important; /* Lebih tinggi dari modal */
}

.select2-container {
    z-index: 9999;
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

.stock-warning {
    color: #dc3545;
    font-size: 0.85em;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    console.log('🚀 Multi-Item Barang Keluar loaded');

    let itemCounter = {{ old('barang_items') ? count(old('barang_items')) : 1 }};

    /* ==================== CUSTOMER SELECT2 ==================== */
    /* ==================== CUSTOMER SELECT2 ==================== */
$('#customerSelect').select2({
    placeholder: 'Pilih pelanggan...',
    allowClear: true,
    width: '100%',
    ajax: {
        url: "{{ route('barang-keluar.autocomplete-customer') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term || '',
                _token: "{{ csrf_token() }}"
            };
        },
        processResults: function (response) {
            console.log('Customer response:', response); // Untuk debugging
            return {
                results: response.results || []
            };
        },
        cache: true
    },
    minimumInputLength: 0, // Biarkan bisa search tanpa input
}).on('select2:open', function () {
    // Trigger search dengan string kosong untuk menampilkan semua
    $('.select2-search__field').val('').trigger('input');
}).on('select2:select', function (e) {
    console.log('Customer selected:', e.params.data);
});

    /* ==================== TOGGLE CUSTOMER MODE ==================== */
    $('input[name="customer_mode"]').change(function () {
        const mode = $(this).val();

        if (mode === 'existing') {
            $('#customer_existing_container').removeClass('d-none');
            $('#customer_manual_container').addClass('d-none');
            $('#customerSelect').prop('disabled', false).prop('required', true);
            $('#customerManualInput, #customer_hp')
                .prop('disabled', true)
                .prop('required', false);
        } else {
            $('#customer_existing_container').addClass('d-none');
            $('#customer_manual_container').removeClass('d-none');
            $('#customerSelect').prop('disabled', true).prop('required', false);
            $('#customerManualInput').prop('disabled', false).prop('required', true);
            $('#customer_hp').prop('disabled', false);

            setTimeout(() => $('#customerManualInput').focus(), 100);
        }
    });

    /* ==================== ADD NEW BARANG ROW ==================== */
$('#addBarangBtn').click(function() {
    console.log('Tambah barang diklik'); // Debug
    
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
            <div class="stock-warning mt-1 d-none"></div>
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
    
    // Set focus ke barang select
    setTimeout(() => {
        $('#barangItems tr:last .select2-barang').select2('open');
    }, 100);
});

    /* ==================== INITIALIZE ROW ==================== */
function initializeRow($row) {
    const index = $row.data('index');
    
    /* === TOGGLE BARANG MODE === */
    $row.find('.barang-mode').change(function() {
        const mode = $(this).val();
        const $existing = $row.find('.existing-barang-container');
        const $manual = $row.find('.manual-barang-container');
        
        if (mode === 'existing') {
            $existing.removeClass('d-none');
            $manual.addClass('d-none');
            $row.find('.barang-select').prop('required', true);
        } else {
            $existing.addClass('d-none');
            $manual.removeClass('d-none');
            $row.find('.barang-select').prop('required', false);
            updateManualUnitSelect($row);
        }
    });
    
    /* === BARANG SELECT2 === */
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
                console.log('Barang response:', response); // Debug
                return {
                    results: response.results || []
                };
            },
            cache: true
        },
        minimumInputLength: 0, // Biarkan bisa search tanpa input
        templateResult: function (data) {
            if (data.loading) return data.text;

            const stokStatus = data.stok_status || 'bg-secondary';
            const stokText =
                data.stok <= 0 ? 'Habis' :
                data.stok <= 10 ? 'Kritis' : 'Aman';

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
        templateSelection: function (data) {
            if (!data.id) return data.text;
            return data.nama || data.text;
        }
    }).on('select2:open', function () {
        // Trigger search dengan string kosong untuk menampilkan semua
        $('.select2-search__field').val('').trigger('input');
    }).on('select2:select', function (e) {
        const data = e.params.data;
        const $row = $(this).closest('tr');
        console.log('Barang selected:', data); // Debug

        // Auto-fill harga_jual jika tersedia dan > 0
        if (data.harga_jual && parseFloat(data.harga_jual) > 0) {
            $row.find('.harga-input').val(data.harga_jual).trigger('input');
        } else {
            $row.find('.harga-input').val('').trigger('input');
        }

        updateUnitSelect($row, data);
        checkStockAvailability($row, data);
        calculateRowTotal($row);
    }).on('select2:clear', function () {
        const $row = $(this).closest('tr');
        $row.find('.unit-select').html('<option value="">Pilih...</option>');
        $row.find('.stock-warning').addClass('d-none');
    });
    
    /* === SATUAN MANUAL === */
    $row.find('.select2-satuan-sm').select2({
        placeholder: 'Satuan',
        width: '100%',
        minimumResultsForSearch: 10
    }).on('select2:select', function () {
        updateManualUnitSelect($row);
    });
    
    /* === INPUT HITUNG === */
    $row.find('.jumlah-input, .harga-input').on('input', function () {
        calculateRowTotal($row);
        updateSummary();

        const selected = $row.find('.select2-barang').select2('data')[0];
        if (selected) {
            checkStockAvailability($row, selected);
        }
    });
    
    /* === REMOVE ROW === */
    $row.find('.remove-item').on('click', function () {
        if ($('#barangItems tr').length > 1) {
            $row.remove();
            updateRowNumbers();
            updateSummary();
        }
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

    function updateRowNumbers() {
    $('.barang-item').each(function (i) {
        $(this).find('td:first').text(i + 1);
    });
    updateRemoveButtons(); // Tambahkan ini
}

    /* ==================== HELPER ==================== */
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
                    : `1 ${baseUnit} = ${(1 / unit.multiplier).toFixed(2)} ${unit.unit_name}`;

                options += `<option value="${unit.unit_name}">${unit.unit_name} (${text})</option>`;
            }
        });

        $unitSelect.html(options).val(baseUnit).trigger('change');
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

    function checkStockAvailability($row, data) {
        const jumlah = parseFloat($row.find('.jumlah-input').val()) || 0;
        const stok = data.stok || 0;
        const satuan = data.satuan || '';
        const $warning = $row.find('.stock-warning');

        if (jumlah > stok) {
            $warning.removeClass('d-none')
                .text(`⚠️ Stok tidak cukup! Tersedia ${stok} ${satuan}`);
        } else {
            $warning.addClass('d-none');
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
        });
    }

    /* ==================== INIT EXISTING ==================== */
    $('.barang-item').each(function () {
        initializeRow($(this));
    });

    updateSummary();
    updateRowNumbers();

    console.log('✅ Multi-Item Barang Keluar initialized successfully!');
});
</script>
@endpush