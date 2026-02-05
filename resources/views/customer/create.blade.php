@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Card Form -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus"></i> Tambah Pelanggan Baru
                    </h4>
                    <a href="{{ route('customer.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Form Tambah Pelanggan -->
                    <form action="{{ route('customer.store') }}" method="POST" id="customerForm">
                        @csrf
                        
                        <!-- Info Kode Pelanggan -->
                        <div class="alert alert-success">
                            <i class="bi bi-info-circle"></i>
                            <strong>Info:</strong> Kode pelanggan akan digenerate otomatis oleh sistem.
                        </div>
                        
                        <!-- Nama Pelanggan -->
                        <div class="mb-4">
                            <label for="nama_customer" class="form-label">
                                Nama Pelanggan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <input type="text" 
                                       name="nama_customer" 
                                       id="nama_customer" 
                                       class="form-control @error('nama_customer') is-invalid @enderror" 
                                       value="{{ old('nama_customer') }}" 
                                       placeholder="Masukkan nama pelanggan" 
                                       required
                                       autofocus>
                                @error('nama_customer')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Contoh: Budi Santoso, CV. Makmur Jaya, Toko Sumber Rejeki</div>
                        </div>
                        
                        <!-- No HP -->
                        <div class="mb-4">
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
                                       placeholder="081234567890"
                                       maxlength="20">
                                @error('no_hp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 0812-3456-7890 atau 081234567890</div>
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
                                          placeholder="Masukkan alamat lengkap pelanggan">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Alamat lengkap termasuk RT/RW, kelurahan, kecamatan, kota</div>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <div>
                                <button type="reset" class="btn btn-secondary" id="resetBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('customer.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-save"></i> Simpan Pelanggan
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
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-asterisk text-danger"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Wajib Diisi</h6>
                                    <p class="mb-0 small">Hanya field dengan tanda bintang (*) yang wajib diisi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex mt-3 mt-md-0">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-history text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Data Historis</h6>
                                    <p class="mb-0 small">Data pelanggan akan tercatat dalam riwayat transaksi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex mt-3 mt-md-0">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shield-check text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Data Aman</h6>
                                    <p class="mb-0 small">Data akan disimpan dengan aman dan dapat diedit.</p>
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
        border-color: #75b798;
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.08);
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .alert-success {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerForm = document.getElementById('customerForm');
        const phoneInput = document.getElementById('no_hp');
        const resetBtn = document.getElementById('resetBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        // Auto format phone number saat input
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
        
        // Clean phone number format saat form submit
        customerForm.addEventListener('submit', function(e) {
            // Validasi nama customer wajib diisi
            const namaCustomer = document.getElementById('nama_customer').value.trim();
            
            if (!namaCustomer) {
                e.preventDefault();
                showAlert('error', 'Nama pelanggan wajib diisi!');
                document.getElementById('nama_customer').focus();
                return false;
            }
            
            // Hapus format dash dari nomor HP sebelum submit
            if (phoneInput.value) {
                phoneInput.value = phoneInput.value.replace(/-/g, '');
            }
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            submitBtn.disabled = true;
        });
        
        // Reset form confirmation
        resetBtn.addEventListener('click', function(e) {
            if (!customerForm.checkValidity()) {
                // Jika form belum diisi, reset langsung
                return true;
            }
            
            if (confirm('Apakah Anda yakin ingin mereset semua data yang telah diisi?')) {
                customerForm.reset();
                document.getElementById('nama_customer').focus();
            }
        });
        
        // Function untuk show alert
        function showAlert(type, message) {
            // Hapus alert yang sudah ada
            const existingAlert = document.querySelector('.custom-alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Buat alert baru
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show custom-alert position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove setelah 5 detik
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Focus management
        const inputs = customerForm.querySelectorAll('input, textarea, select');
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });
        });
        
        // Auto capitalize nama customer
        const namaCustomerInput = document.getElementById('nama_customer');
        namaCustomerInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = e.target.value
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                    .join(' ');
            }
        });
    });
</script>
@endpush