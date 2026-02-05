@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Card Form -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus"></i> Tambah Supplier Baru
                    </h4>
                    <a href="{{ route('supplier.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Form Tambah Supplier -->
                    <form action="{{ route('supplier.store') }}" method="POST" id="supplierForm">
                        @csrf
                        
                        <!-- Info Kode Supplier -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Info:</strong> Kode supplier akan digenerate otomatis oleh sistem.
                        </div>
                        
                        <!-- Nama Supplier -->
                        <div class="mb-4">
                            <label for="nama_supplier" class="form-label">
                                Nama Supplier <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <input type="text" 
                                       name="nama_supplier" 
                                       id="nama_supplier" 
                                       class="form-control @error('nama_supplier') is-invalid @enderror" 
                                       value="{{ old('nama_supplier') }}" 
                                       placeholder="Masukkan nama supplier" 
                                       required>
                                @error('nama_supplier')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Contoh: PT. Sumber Makmur Jaya, CV. Abadi Sentosa</div>
                        </div>
                        
                        <!-- Alamat -->
                        <div class="mb-4">
                            <label for="alamat" class="form-label">
                                Alamat <span class="text-muted">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <textarea name="alamat" 
                                          id="alamat" 
                                          class="form-control @error('alamat') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Masukkan alamat lengkap supplier">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Alamat lengkap termasuk kota dan kode pos</div>
                        </div>
                        
                        <!-- No HP dan Email -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label">
                                    No. Handphone <span class="text-muted">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-telephone"></i>
                                    </span>
                                    <input type="tel" 
                                           name="no_hp" 
                                           id="no_hp" 
                                           class="form-control @error('no_hp') is-invalid @enderror" 
                                           value="{{ old('no_hp') }}" 
                                           placeholder="081234567890">
                                    @error('no_hp')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">Format: 0812-3456-7890</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="text-muted">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           placeholder="supplier@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">Email valid untuk komunikasi</div>
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <div>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Supplier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Informasi Penting
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Wajib Diisi</h6>
                                    <p class="mb-0 small">Hanya field dengan tanda bintang (*) yang wajib diisi. Field lainnya bersifat opsional.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mt-3 mt-md-0">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shield-check text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Data Aman</h6>
                                    <p class="mb-0 small">Data supplier akan disimpan dengan aman dan dapat diedit kapan saja.</p>
                                </div>
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
<style>
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    .form-control:focus + .input-group-text {
        border-color: #86b7fe;
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.08);
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and auto format phone number
    document.addEventListener('DOMContentLoaded', function() {
        const supplierForm = document.getElementById('supplierForm');
        const phoneInput = document.getElementById('no_hp');
        
        // Auto format phone number
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 0) {
                if (value.length <= 4) {
                    value = value;
                } else if (value.length <= 8) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                } else if (value.length <= 12) {
                    value = value.substring(0, 4) + '-' + value.substring(4, 8) + '-' + value.substring(8);
                } else {
                    value = value.substring(0, 4) + '-' + value.substring(4, 8) + '-' + value.substring(8, 12);
                }
            }
            
            e.target.value = value;
        });
        
        // Form validation
        supplierForm.addEventListener('submit', function(e) {
            const namaSupplier = document.getElementById('nama_supplier').value.trim();
            
            if (!namaSupplier) {
                e.preventDefault();
                alert('Nama supplier wajib diisi!');
                document.getElementById('nama_supplier').focus();
                return false;
            }
            
            // Validate email format
            const email = document.getElementById('email').value.trim();
            if (email && !isValidEmail(email)) {
                e.preventDefault();
                alert('Format email tidak valid!');
                document.getElementById('email').focus();
                return false;
            }
            
            // Show loading state
            const submitBtn = supplierForm.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
        
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Reset form confirmation
        supplierForm.addEventListener('reset', function(e) {
            if (!confirm('Apakah Anda yakin ingin mereset semua data yang telah diisi?')) {
                e.preventDefault();
            }
        });
        
        // Focus on first input
        document.getElementById('nama_supplier').focus();
    });
</script>
@endpush