@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Tambah Barang Baru
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('barang.store') }}" method="POST" id="formTambahBarang">
                    @csrf

                    {{-- Informasi Dasar --}}
                    <h6 class="border-bottom pb-2 mb-3">Informasi Dasar</h6>

                    {{-- Kode Barang (auto generated tapi bisa ditampilkan) --}}
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Kode barang akan otomatis dibuat oleh sistem.
                        <strong>Kode Barang:</strong> <span class="badge bg-secondary">{{ \App\Models\Barang::generateKode() }}</span>
                    </div>

                    <div class="row">
                        {{-- Nama Barang --}}
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_barang"
                                       class="form-control @error('nama_barang') is-invalid @enderror"
                                       value="{{ old('nama_barang') }}"
                                       placeholder="Contoh: Semen, Cat Tembok, Keramik 40x40"
                                       required
                                       minlength="3">
                                @error('nama_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-nama_barang" style="display: none;"></div>
                            </div>
                        </div>

                        {{-- Stok Minimum --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    Stok Minimum <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       name="stok_minimum"
                                       class="form-control @error('stok_minimum') is-invalid @enderror"
                                       value="{{ old('stok_minimum', 10) }}"
                                       min="0"
                                       required>
                                <small class="text-muted">Peringatan saat stok mencapai angka ini</small>
                                @error('stok_minimum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-stok_minimum" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Satuan Dasar --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Satuan Dasar <span class="text-danger">*</span>
                                </label>
                                <select name="base_unit"
                                        class="form-control select2-satuan @error('base_unit') is-invalid @enderror"
                                        required>
                                    <option value="">Pilih atau ketik satuan...</option>
                                    @isset($allUnits)
                                        @foreach($allUnits as $unit)
                                            <option value="{{ $unit }}"
                                                {{ old('base_unit') === $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    @else
                                        {{-- Default units jika $allUnits tidak ada --}}
                                        @foreach([
                                            'Bag','Batang','Blek','Botol','Box','Buah','Bungkus','Drum','Dus',
                                            'Ember','Galon','Gram','Gulung','Ikat','Jerigen','Kaleng','Karung',
                                            'Kg','Lembar','Liter','Lusin','M2','M3','Meter','Mililiter','Ons',
                                            'Pail','Palet','Pasang','Peti','Pieces','Roll','Sak','Set','Tube','Unit'
                                        ] as $unit)
                                            <option value="{{ $unit }}"
                                                {{ old('base_unit') === $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <small class="text-muted">Satuan terkecil untuk transaksi</small>
                                @error('base_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-base_unit" style="display: none;"></div>
                            </div>
                        </div>

                        {{-- Stok Awal (Opsional untuk barang baru) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    Stok Awal
                                </label>
                                <input type="number"
                                       name="stok_awal"
                                       class="form-control @error('stok_awal') is-invalid @enderror"
                                       value="{{ old('stok_awal', 0) }}"
                                       min="0"
                                       step="0.01">
                                <small class="text-muted">Stok awal barang (default: 0)</small>
                                @error('stok_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Deskripsi barang, spesifikasi, atau catatan tambahan">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Harga Beli & Harga Jual --}}
                    <h6 class="border-bottom pb-2 mb-3 mt-4">Harga Barang (Opsional)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Beli (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="9999999999.99"
                                           name="harga_beli"
                                           class="form-control @error('harga_beli') is-invalid @enderror"
                                           value="{{ old('harga_beli', 0) }}"
                                           placeholder="0 = input manual saat Barang Masuk">
                                </div>
                                <small class="text-muted">Jika 0, harga bisa diinput manual saat <strong>Barang Masuk</strong></small>
                                @error('harga_beli')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-harga_beli" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Jual (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="9999999999.99"
                                           name="harga_jual"
                                           class="form-control @error('harga_jual') is-invalid @enderror"
                                           value="{{ old('harga_jual', 0) }}"
                                           placeholder="0 = input manual saat Barang Keluar">
                                </div>
                                <small class="text-muted">Jika 0, harga bisa diinput manual saat <strong>Barang Keluar</strong></small>
                                @error('harga_jual')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-harga_jual" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Konversi Satuan Tambahan --}}
                    <h6 class="border-bottom pb-2 mb-3 mt-4">
                        Konversi Satuan Tambahan (Opsional)
                        <small class="text-muted">- Untuk memudahkan input transaksi</small>
                    </h6>

                    <div id="units-container">
                        @if(old('units'))
                            @foreach(old('units') as $index => $unit)
                                <div class="row mb-2 unit-row">
                                    <div class="col-md-5">
                                        <input type="text"
                                               name="units[{{ $index }}][name]"
                                               class="form-control unit-name @error('units.'.$index.'.name') is-invalid @enderror"
                                               value="{{ $unit['name'] ?? '' }}"
                                               placeholder="Nama satuan (sak, box, karton)"
                                               minlength="1"
                                               maxlength="20">
                                        @error('units.'.$index.'.name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback unit-error-name" style="display: none;"></div>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number"
                                               step="0.01"
                                               min="0.01"
                                               max="999999"
                                               name="units[{{ $index }}][multiplier]"
                                               class="form-control unit-multiplier @error('units.'.$index.'.multiplier') is-invalid @enderror"
                                               value="{{ $unit['multiplier'] ?? '' }}"
                                               placeholder="1 satuan = ... satuan dasar">
                                        @error('units.'.$index.'.multiplier')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button"
                                                class="btn btn-danger btn-sm remove-unit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="row mb-2 unit-row">
                                <div class="col-md-5">
                                    <input type="text"
                                           name="units[0][name]"
                                           class="form-control unit-name"
                                           placeholder="Nama satuan (sak, box, karton)"
                                           minlength="1"
                                           maxlength="20">
                                    <div class="invalid-feedback unit-error-name" style="display: none;"></div>
                                </div>
                                <div class="col-md-5">
                                    <input type="number"
                                           step="0.01"
                                           min="0.01"
                                           max="999999"
                                           name="units[0][multiplier]"
                                           class="form-control unit-multiplier"
                                           placeholder="1 satuan = ... satuan dasar">
                                    <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-danger btn-sm remove-unit"
                                            disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-secondary mb-3"
                            id="add-unit">
                        <i class="bi bi-plus"></i> Tambah Konversi Satuan
                    </button>

                    <div class="alert alert-info">
                        <strong><i class="bi bi-info-circle"></i> Contoh Konversi:</strong>
                        <ul class="mb-0">
                            <li>Satuan dasar: <code>kg</code> → Satuan tambahan: <code>sak</code> (50)</li>
                            <li>Satuan dasar: <code>liter</code> → Satuan tambahan: <code>kaleng</code> (5)</li>
                            <li>Satuan dasar: <code>pcs</code> → Satuan tambahan: <code>box</code> (12)</li>
                            <li><strong>Catatan:</strong> Satuan dasar (multiplier = 1) sudah otomatis dibuat</li>
                        </ul>
                    </div>

                    {{-- Keterangan --}}
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Stok awal bisa ditambahkan nanti melalui menu "Barang Masuk"</li>
                            <li>Pastikan satuan dasar adalah satuan terkecil yang akan digunakan dalam sistem</li>
                            <li>Stok minimum digunakan untuk sistem peringatan stok menipis</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="button" class="btn btn-primary" id="btnSubmitConfirm">
                            <i class="bi bi-save"></i> Simpan Barang
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Tambah Barang -->
<div class="modal fade" id="confirmCreateModal" tabindex="-1" aria-labelledby="confirmCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmCreateModalLabel">
                    <i class="bi bi-question-circle"></i> Konfirmasi Tambah Barang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-box-seam text-primary" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-3">Apakah Anda yakin ingin menambahkan barang ini?</h5>
                
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Detail Barang:</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="40%"><strong>Nama Barang</strong></td>
                                <td id="confirmNamaBarang">-</td>
                            </tr>
                            <tr>
                                <td><strong>Satuan Dasar</strong></td>
                                <td id="confirmBaseUnit">-</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Minimum</strong></td>
                                <td id="confirmStokMin">-</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Awal</strong></td>
                                <td id="confirmStokAwal">-</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Beli</strong></td>
                                <td id="confirmHargaBeli">-</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Jual</strong></td>
                                <td id="confirmHargaJual">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Informasi:</strong> Barang akan segera ditambahkan ke dalam sistem dan dapat digunakan untuk transaksi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Periksa Kembali
                </button>
                <button type="button" class="btn btn-primary" id="confirmCreateBtn">
                    <i class="bi bi-check-circle"></i> Ya, Tambah Barang
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
        border: 1px solid #ced4da;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
    }
    .unit-row:first-child .remove-unit {
        display: none;
    }
    .is-invalid ~ .select2-container .select2-selection {
        border-color: #dc3545;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Initialize Select2 for units
        $('.select2-satuan').select2({
            placeholder: "Pilih atau ketik satuan...",
            tags: true,
            width: '100%',
            allowClear: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term + ' (baru)',
                    newTag: true
                };
            }
        }).on('select2:select', function (e) {
            $(this).removeClass('is-invalid');
            $('#error-base_unit').hide();
        });
    });

    // Unit conversion management
    let unitIndex = {{ old('units') ? count(old('units')) : 1 }};

    document.getElementById('add-unit').addEventListener('click', function () {
        const container = document.getElementById('units-container');

        const row = document.createElement('div');
        row.className = 'row mb-2 unit-row';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text"
                       name="units[${unitIndex}][name]"
                       class="form-control unit-name"
                       placeholder="Nama satuan (sak, box, karton)"
                       minlength="1"
                       maxlength="20">
                <div class="invalid-feedback unit-error-name" style="display: none;"></div>
            </div>
            <div class="col-md-5">
                <input type="number"
                       step="0.01"
                       min="0.01"
                       max="999999"
                       name="units[${unitIndex}][multiplier]"
                       class="form-control unit-multiplier"
                       placeholder="1 satuan = ... satuan dasar">
                <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
            </div>
            <div class="col-md-2">
                <button type="button"
                        class="btn btn-danger btn-sm remove-unit">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(row);
        unitIndex++;

        // Update remove button state
        updateRemoveButtons();
    });

    // Remove unit row
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-unit')) {
            const row = e.target.closest('.unit-row');
            row.remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.unit-row');
        const removeButtons = document.querySelectorAll('.remove-unit');
        
        if (rows.length <= 1) {
            removeButtons.forEach(btn => btn.disabled = true);
        } else {
            removeButtons.forEach(btn => btn.disabled = false);
        }
    }

    // Form validation and confirmation
    const confirmCreateModal = new bootstrap.Modal(document.getElementById('confirmCreateModal'));
    const form = document.getElementById('formTambahBarang');
    
    document.getElementById('btnSubmitConfirm').addEventListener('click', function() {
        if (validateForm()) {
            // Set data untuk modal konfirmasi
            setConfirmModalData();
            confirmCreateModal.show();
        }
    });
    
    document.getElementById('confirmCreateBtn').addEventListener('click', function() {
        // Show loading
        const submitBtn = document.getElementById('btnSubmitConfirm');
        const confirmBtn = document.getElementById('confirmCreateBtn');
        
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        submitBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        confirmBtn.disabled = true;
        
        // Tutup modal dan submit form
        confirmCreateModal.hide();
        
        // Submit form setelah delay kecil
        setTimeout(() => {
            form.submit();
        }, 300);
    });
    
    function validateForm() {
        let isValid = true;
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.style.display = 'none';
        });
        
        // Validate required fields
        const namaBarang = form.querySelector('[name="nama_barang"]');
        if (!namaBarang.value.trim() || namaBarang.value.length < 3) {
            showError(namaBarang, 'Nama barang minimal 3 karakter');
            isValid = false;
        }
        
        const stokMinimum = form.querySelector('[name="stok_minimum"]');
        if (!stokMinimum.value || stokMinimum.value < 0) {
            showError(stokMinimum, 'Stok minimum harus diisi dan tidak boleh negatif');
            isValid = false;
        }
        
        const baseUnit = form.querySelector('.select2-satuan');
        if (!baseUnit.value) {
            showError(baseUnit, 'Satuan dasar wajib dipilih');
            isValid = false;
        }
        
        // Validate harga
        const hargaBeli = form.querySelector('[name="harga_beli"]');
        if (hargaBeli.value && hargaBeli.value < 0) {
            showError(hargaBeli, 'Harga beli tidak boleh negatif');
            isValid = false;
        }
        
        const hargaJual = form.querySelector('[name="harga_jual"]');
        if (hargaJual.value && hargaJual.value < 0) {
            showError(hargaJual, 'Harga jual tidak boleh negatif');
            isValid = false;
        }
        
        // Validate units
        const unitNames = new Set();
        document.querySelectorAll('.unit-row').forEach((row, index) => {
            const unitNameInput = row.querySelector('.unit-name');
            const multiplierInput = row.querySelector('.unit-multiplier');
            
            if (unitNameInput.value || multiplierInput.value) {
                // Both must be filled
                if (!unitNameInput.value) {
                    showUnitError(row, 'name', 'Nama satuan harus diisi');
                    isValid = false;
                }
                if (!multiplierInput.value) {
                    showUnitError(row, 'multiplier', 'Multiplier harus diisi');
                    isValid = false;
                }
                
                if (multiplierInput.value && multiplierInput.value < 0.01) {
                    showUnitError(row, 'multiplier', 'Multiplier minimal 0.01');
                    isValid = false;
                }
                
                // Check for duplicate unit names
                if (unitNameInput.value) {
                    const lowerName = unitNameInput.value.toLowerCase();
                    if (unitNames.has(lowerName)) {
                        showUnitError(row, 'name', 'Satuan tidak boleh duplikat');
                        isValid = false;
                    }
                    unitNames.add(lowerName);
                }
                
                // Check if unit name same as base unit
                if (unitNameInput.value && baseUnit.value && 
                    unitNameInput.value.toLowerCase() === baseUnit.value.toLowerCase()) {
                    showUnitError(row, 'name', 'Satuan tidak boleh sama dengan satuan dasar');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    function setConfirmModalData() {
        // Ambil data dari form
        const namaBarang = form.querySelector('[name="nama_barang"]').value;
        const baseUnit = form.querySelector('.select2-satuan').value;
        const stokMinimum = form.querySelector('[name="stok_minimum"]').value;
        const stokAwal = form.querySelector('[name="stok_awal"]').value || '0';
        const hargaBeli = form.querySelector('[name="harga_beli"]').value;
        const hargaJual = form.querySelector('[name="harga_jual"]').value;
        
        // Tampilkan di modal
        document.getElementById('confirmNamaBarang').textContent = namaBarang || '-';
        document.getElementById('confirmBaseUnit').textContent = baseUnit || '-';
        document.getElementById('confirmStokMin').textContent = stokMinimum ? `${stokMinimum} ${baseUnit}` : '-';
        document.getElementById('confirmStokAwal').textContent = stokAwal ? `${stokAwal} ${baseUnit}` : '-';
        document.getElementById('confirmHargaBeli').textContent = hargaBeli ? formatRupiah(hargaBeli) : 'Rp 0 (input manual nanti)';
        document.getElementById('confirmHargaJual').textContent = hargaJual ? formatRupiah(hargaJual) : 'Rp 0 (input manual nanti)';
    }
    
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        const number = parseFloat(angka);
        return 'Rp ' + number.toLocaleString('id-ID');
    }
    
    function showError(element, message) {
        element.classList.add('is-invalid');
        const errorDiv = document.getElementById('error-' + element.name);
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
        
        // Scroll ke error
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // SweetAlert untuk error
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }
    
    function showUnitError(row, type, message) {
        const errorDiv = row.querySelector('.unit-error-' + type);
        const input = row.querySelector(type === 'name' ? '.unit-name' : '.unit-multiplier');
        input.classList.add('is-invalid');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        // Scroll ke error
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // SweetAlert untuk error
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    // Initialize remove button state
    updateRemoveButtons();
</script>
@endpush