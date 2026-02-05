@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('barang.update', $barang) }}" method="POST" id="formEditBarang">
                    @csrf
                    @method('PUT')
                    
                    <!-- Kode Barang (Read Only) -->
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" class="form-control bg-light" value="{{ $barang->kode_barang }}" readonly>
                    </div>
                    
                    <!-- Informasi Dasar -->
                    <h6 class="border-bottom pb-2 mb-3">Informasi Dasar</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" 
                               value="{{ old('nama_barang', $barang->nama_barang) }}" required minlength="3">
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="error-nama_barang" style="display: none;"></div>
                    </div>
                    
                    <div class="row">
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
                                                {{ old('base_unit', $barang->base_unit) === $unit ? 'selected' : '' }}>
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
                                                {{ old('base_unit', $barang->base_unit) === $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('base_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-base_unit" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" 
                                       value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required>
                                @error('stok_minimum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-stok_minimum" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok Sekarang</label>
                        <input type="text" class="form-control bg-light" value="{{ number_format($barang->stok_sekarang, 2) }} {{ $barang->base_unit }}" readonly>
                        <small class="text-muted">Stok tidak bisa diubah langsung, gunakan Barang Masuk/Keluar</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                    </div>

                    <!-- Harga Beli & Harga Jual -->
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
                                           value="{{ old('harga_beli', $barang->harga_beli) }}">
                                </div>
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
                                           value="{{ old('harga_jual', $barang->harga_jual) }}">
                                </div>
                                @error('harga_jual')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="error-harga_jual" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Konversi Satuan -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4">
                        Konversi Satuan (Opsional)
                        <small class="text-muted">- Untuk memudahkan input barang</small>
                    </h6>
                    
                    <div id="units-container">
                        @php
                            $oldUnits = old('units');
                            $itemUnits = $barang->itemUnits->where('is_base', false);
                            $index = 0;
                        @endphp
                        
                        @if($oldUnits)
                            @foreach($oldUnits as $index => $unit)
                            <div class="row mb-2 unit-row">
                                <div class="col-md-5">
                                    <input type="text" name="units[{{ $index }}][name]" 
                                           class="form-control unit-name @error('units.'.$index.'.name') is-invalid @enderror" 
                                           placeholder="Nama satuan (sak, box, karton)" 
                                           value="{{ $unit['name'] ?? '' }}"
                                           minlength="1"
                                           maxlength="20">
                                    @error('units.'.$index.'.name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback unit-error-name" style="display: none;"></div>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" step="0.01" min="0.01" max="999999"
                                           name="units[{{ $index }}][multiplier]" 
                                           class="form-control unit-multiplier @error('units.'.$index.'.multiplier') is-invalid @enderror" 
                                           placeholder="1 satuan ini = ... satuan dasar" 
                                           value="{{ $unit['multiplier'] ?? '' }}">
                                    @error('units.'.$index.'.multiplier')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-unit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            @foreach($itemUnits as $index => $unit)
                            <div class="row mb-2 unit-row">
                                <div class="col-md-5">
                                    <input type="text" name="units[{{ $index }}][name]" 
                                           class="form-control unit-name" 
                                           placeholder="Nama satuan (sak, box, karton)" 
                                           value="{{ $unit->unit_name }}"
                                           minlength="1"
                                           maxlength="20">
                                    <div class="invalid-feedback unit-error-name" style="display: none;"></div>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" step="0.01" min="0.01" max="999999"
                                           name="units[{{ $index }}][multiplier]" 
                                           class="form-control unit-multiplier" 
                                           placeholder="1 satuan ini = ... satuan dasar" 
                                           value="{{ $unit->multiplier }}">
                                    <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-unit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @endif
                        
                        @if(($oldUnits && count($oldUnits) == 0) || (!$oldUnits && $itemUnits->count() == 0))
                        <div class="row mb-2 unit-row">
                            <div class="col-md-5">
                                <input type="text" name="units[0][name]" 
                                       class="form-control unit-name" 
                                       placeholder="Nama satuan (sak, box, karton)"
                                       minlength="1"
                                       maxlength="20">
                                <div class="invalid-feedback unit-error-name" style="display: none;"></div>
                            </div>
                            <div class="col-md-5">
                                <input type="number" step="0.01" min="0.01" max="999999"
                                       name="units[0][multiplier]" 
                                       class="form-control unit-multiplier" 
                                       placeholder="1 satuan ini = ... satuan dasar">
                                <div class="invalid-feedback unit-error-multiplier" style="display: none;"></div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-unit" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-secondary mb-3" id="add-unit">
                        <i class="bi bi-plus"></i> Tambah Satuan
                    </button>
                    
                    <div class="alert alert-info">
                        <strong><i class="bi bi-info-circle"></i> Contoh:</strong>
                        <ul class="mb-0">
                            <li>Semen: Satuan dasar <code>kg</code>, tambah satuan <code>sak</code> dengan multiplier <code>50</code></li>
                            <li>Cat: Satuan dasar <code>liter</code>, tambah satuan <code>kaleng</code> dengan multiplier <code>5</code></li>
                        </ul>
                    </div>
                    
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="button" class="btn btn-warning text-dark" id="btnSubmitConfirm">
                            <i class="bi bi-save"></i> Update Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Edit Barang -->
<div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="confirmEditModalLabel">
                    <i class="bi bi-question-circle"></i> Konfirmasi Update Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-pencil-square text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-3">Apakah Anda yakin ingin mengubah data barang ini?</h5>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Kode Barang: <span id="confirmKodeBarang">{{ $barang->kode_barang }}</span></strong>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Perubahan Data:</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="40%"><strong>Nama Barang</strong></td>
                                <td>
                                    <div><del id="oldNamaBarang">{{ $barang->nama_barang }}</del></div>
                                    <div><strong>→</strong> <span id="newNamaBarang">-</span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Satuan Dasar</strong></td>
                                <td>
                                    <div><del id="oldBaseUnit">{{ $barang->base_unit }}</del></div>
                                    <div><strong>→</strong> <span id="newBaseUnit">-</span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Stok Minimum</strong></td>
                                <td>
                                    <div><del id="oldStokMin">{{ $barang->stok_minimum }} {{ $barang->base_unit }}</del></div>
                                    <div><strong>→</strong> <span id="newStokMin">-</span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Harga Beli</strong></td>
                                <td>
                                    <div><del id="oldHargaBeli">{{ $barang->harga_beli ? 'Rp ' . number_format($barang->harga_beli, 0, ',', '.') : 'Rp 0' }}</del></div>
                                    <div><strong>→</strong> <span id="newHargaBeli">-</span></div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Harga Jual</strong></td>
                                <td>
                                    <div><del id="oldHargaJual">{{ $barang->harga_jual ? 'Rp ' . number_format($barang->harga_jual, 0, ',', '.') : 'Rp 0' }}</del></div>
                                    <div><strong>→</strong> <span id="newHargaJual">-</span></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Perubahan data barang akan mempengaruhi transaksi yang menggunakan barang ini.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Periksa Kembali
                </button>
                <button type="button" class="btn btn-warning text-dark" id="confirmEditBtn">
                    <i class="bi bi-check-circle"></i> Ya, Update Barang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('.select2-satuan').select2({
            placeholder: "Pilih atau ketik satuan...",
            tags: true,
            width: '100%'
        }).on('select2:select', function (e) {
            $(this).removeClass('is-invalid');
            $('#error-base_unit').hide();
        });
    });

    let unitIndex = {{ $oldUnits ? count($oldUnits) : ($itemUnits ? $itemUnits->count() : 1) }};
    if (unitIndex === 0) unitIndex = 1;

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
                       placeholder="1 satuan ini = ... satuan dasar">
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

        document.querySelectorAll('.remove-unit').forEach(btn => btn.disabled = false);
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-unit')) {
            const row = e.target.closest('.unit-row');
            row.remove();

            const rows = document.querySelectorAll('.unit-row');
            if (rows.length === 1) {
                rows[0].querySelector('.remove-unit').disabled = true;
            }
        }
    });

    // Form validation and confirmation for edit
    const confirmEditModal = new bootstrap.Modal(document.getElementById('confirmEditModal'));
    const form = document.getElementById('formEditBarang');
    const originalData = {
        nama_barang: "{{ $barang->nama_barang }}",
        base_unit: "{{ $barang->base_unit }}",
        stok_minimum: "{{ $barang->stok_minimum }}",
        harga_beli: "{{ $barang->harga_beli }}",
        harga_jual: "{{ $barang->harga_jual }}"
    };
    
    document.getElementById('btnSubmitConfirm').addEventListener('click', function() {
        if (validateEditForm()) {
            // Set data untuk modal konfirmasi
            setEditConfirmModalData();
            confirmEditModal.show();
        }
    });
    
    document.getElementById('confirmEditBtn').addEventListener('click', function() {
        // Show loading
        const submitBtn = document.getElementById('btnSubmitConfirm');
        const confirmBtn = document.getElementById('confirmEditBtn');
        
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        submitBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        confirmBtn.disabled = true;
        
        // Tutup modal dan submit form
        confirmEditModal.hide();
        
        // Submit form setelah delay kecil
        setTimeout(() => {
            form.submit();
        }, 300);
    });
    
    function validateEditForm() {
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
    
    function setEditConfirmModalData() {
        // Ambil data dari form
        const namaBarang = form.querySelector('[name="nama_barang"]').value;
        const baseUnit = form.querySelector('.select2-satuan').value;
        const stokMinimum = form.querySelector('[name="stok_minimum"]').value;
        const hargaBeli = form.querySelector('[name="harga_beli"]').value || '0';
        const hargaJual = form.querySelector('[name="harga_jual"]').value || '0';
        
        // Tampilkan data lama dan baru di modal
        document.getElementById('newNamaBarang').textContent = namaBarang;
        document.getElementById('newBaseUnit').textContent = baseUnit;
        document.getElementById('newStokMin').textContent = `${stokMinimum} ${baseUnit}`;
        document.getElementById('newHargaBeli').textContent = formatRupiah(hargaBeli);
        document.getElementById('newHargaJual').textContent = formatRupiah(hargaJual);
        
        // Tampilkan data lama (sudah di-set di HTML)
        document.getElementById('oldNamaBarang').textContent = originalData.nama_barang;
        document.getElementById('oldBaseUnit').textContent = originalData.base_unit;
        document.getElementById('oldStokMin').textContent = `${originalData.stok_minimum} ${originalData.base_unit}`;
        document.getElementById('oldHargaBeli').textContent = formatRupiah(originalData.harga_beli);
        document.getElementById('oldHargaJual').textContent = formatRupiah(originalData.harga_jual);
    }
    
    function formatRupiah(angka) {
        if (!angka || angka === '0') return 'Rp 0';
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
    const rows = document.querySelectorAll('.unit-row');
    if (rows.length <= 1) {
        document.querySelectorAll('.remove-unit').forEach(btn => btn.disabled = true);
    }
</script>
@endpush