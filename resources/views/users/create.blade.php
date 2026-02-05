@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 text-blue">
                        <i class="bi bi-person-plus me-2"></i>Tambah User Baru
                    </h4>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">User Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar User
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Utama -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-gradient-blue text-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-person-plus me-2"></i>
                        Form Tambah User Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST" id="userForm">
                        @csrf

                        <!-- Informasi Dasar -->
                        <div class="mb-4">
                            <h6 class="text-blue mb-3 pb-2 border-bottom border-blue">
                                <i class="bi bi-person-circle me-2"></i>Informasi Dasar
                            </h6>
                            <div class="row">
                                <!-- Nama -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-person text-blue"></i>
                                        </span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" 
                                               placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-envelope text-blue"></i>
                                        </span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" 
                                               placeholder="nama@perusahaan.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nomor Telepon -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        Nomor Telepon
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-phone text-blue"></i>
                                        </span>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}" 
                                               placeholder="081234567890">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label fw-semibold">
                                        Peran (Role) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-shield-check text-blue"></i>
                                        </span>
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" name="role" required onchange="togglePermissions()">
                                            <option value="">-- Pilih Role --</option>
                                            @foreach($roles as $value => $label)
                                                <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <h6 class="text-blue mb-3 pb-2 border-bottom border-blue">
                                <i class="bi bi-key me-2"></i>Keamanan Akun
                            </h6>
                            <div class="row">
                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-lock text-blue"></i>
                                        </span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text mt-1">
                                        <small>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka
                                        </small>
                                    </div>
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-lock-fill text-blue"></i>
                                        </span>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text mt-1">
                                        <small>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Ketik ulang password untuk konfirmasi
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Section -->
                        <div class="mb-4" id="permissions-section" style="display: none;">
                            <h6 class="text-blue mb-3 pb-2 border-bottom border-blue">
                                <i class="bi bi-list-check me-2"></i>Izin Akses (Permissions)
                            </h6>
                            
                            <div class="alert alert-info border-start-4 border-start-info">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                    <div>
                                        <small>
                                            Pilih menu dan fitur yang dapat diakses oleh user ini. 
                                            Jika tidak ada yang dipilih, user tidak akan dapat mengakses apapun.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @php
                                    $permissionGroups = [
                                        'Dashboard' => [
                                            'icon' => 'bi-speedometer2',
                                            'perms' => ['view_dashboard']
                                        ],
                                        'Data Barang' => [
                                            'icon' => 'bi-box-seam',
                                            'perms' => ['view_barang', 'create_barang', 'edit_barang', 'delete_barang']
                                        ],
                                        'Barang Masuk' => [
                                            'icon' => 'bi-box-arrow-in-down',
                                            'perms' => ['view_barang_masuk', 'create_barang_masuk', 'edit_barang_masuk', 'delete_barang_masuk']
                                        ],
                                        'Barang Keluar' => [
                                            'icon' => 'bi-box-arrow-up',
                                            'perms' => ['view_barang_keluar', 'create_barang_keluar', 'edit_barang_keluar', 'delete_barang_keluar']
                                        ],
                                        'Supplier' => [
                                            'icon' => 'bi-truck',
                                            'perms' => ['view_supplier', 'create_supplier', 'edit_supplier', 'delete_supplier']
                                        ],
                                        'Pelanggan' => [
                                            'icon' => 'bi-people',
                                            'perms' => ['view_customer', 'create_customer', 'edit_customer', 'delete_customer']
                                        ],
                                        'Laporan' => [
                                            'icon' => 'bi-file-earmark-text',
                                            'perms' => ['view_laporan_transaksi', 'view_laporan_bulanan', 'view_laporan_pergerakan_barang', 'view_laporan_barang_tidak_laku']
                                        ],
                                        'Manajemen User' => [
                                            'icon' => 'bi-person-badge',
                                            'perms' => ['view_users', 'create_users', 'edit_users', 'delete_users']
                                        ],
                                    ];
                                @endphp

                                @foreach($permissionGroups as $group => $data)
                                <div class="col-lg-6 mb-3">
                                    <div class="card h-100 border">
                                        <div class="card-header bg-light d-flex align-items-center">
                                            <i class="bi {{ $data['icon'] }} me-2 text-blue"></i>
                                            <h6 class="mb-0 fw-semibold">{{ $group }}</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            @foreach($data['perms'] as $perm)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[]" value="{{ $perm }}" 
                                                       id="{{ $perm }}"
                                                       {{ in_array($perm, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="{{ $perm }}">
                                                    {{ $permissions[$perm] ?? $perm }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-4">
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Pastikan semua data sudah benar sebelum disimpan
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-circle me-2"></i>Simpan User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <!-- Informasi Role -->
            <div class="card mb-4 border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-info-circle me-2 text-blue"></i>
                        Informasi Peran (Role)
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Owner/Admin -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="p-2 rounded bg-danger bg-opacity-10 me-3">
                                <i class="bi bi-shield-fill-check text-danger fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-danger">Owner/Admin</h6>
                            </div>
                        </div>
                        <div class="ms-5">
                            <small class="text-muted">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                Akses penuh ke semua fitur sistem
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                Tidak perlu memilih permissions manual
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                Dapat mengatur semua pengguna
                            </small>
                        </div>
                    </div>

                    <!-- Kasir -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="p-2 rounded bg-primary bg-opacity-10 me-3">
                                <i class="bi bi-cash-stack text-primary fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-primary">Kasir</h6>
                            </div>
                        </div>
                        <div class="ms-5">
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Pilih menu & fitur spesifik yang diizinkan
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Fokus pada transaksi penjualan
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Akses terbatas sesuai kebutuhan
                            </small>
                        </div>
                    </div>

                    <!-- Staff Gudang -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="p-2 rounded bg-info bg-opacity-10 me-3">
                                <i class="bi bi-box-seam-fill text-info fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-info">Staff Gudang</h6>
                            </div>
                        </div>
                        <div class="ms-5">
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Pilih menu & fitur spesifik yang diizinkan
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Fokus pada manajemen stok barang
                            </small><br>
                            <small class="text-muted">
                                <i class="bi bi-gear-fill text-info me-1"></i>
                                Akses terbatas sesuai kebutuhan
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Cepat -->
            <div class="card border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>
                        Panduan Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0 py-2">
                            <small>
                                <i class="bi bi-1-circle-fill text-primary me-2"></i>
                                Isi semua informasi dasar user
                            </small>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2">
                            <small>
                                <i class="bi bi-2-circle-fill text-primary me-2"></i>
                                Pilih role sesuai kebutuhan
                            </small>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2">
                            <small>
                                <i class="bi bi-3-circle-fill text-primary me-2"></i>
                                Atur password yang kuat
                            </small>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2">
                            <small>
                                <i class="bi bi-4-circle-fill text-primary me-2"></i>
                                Tentukan permissions (jika bukan owner)
                            </small>
                        </div>
                        <div class="list-group-item border-0 px-0 py-2">
                            <small>
                                <i class="bi bi-5-circle-fill text-primary me-2"></i>
                                Review dan simpan data
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-blue {
        border-color: #3b82f6 !important;
    }
    
    .text-blue {
        color: #1e40af !important;
    }
    
    .card-header.bg-gradient-blue {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
    }
    
    .form-check-input:checked {
        background-color: #1e40af;
        border-color: #1e40af;
    }
    
    .input-group-text {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }
    
    .alert-info {
        background-color: #dbeafe;
        border-color: #bfdbfe;
        color: #1e40af;
    }
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
</style>

<script>
function togglePermissions() {
    const role = document.getElementById('role').value;
    const permissionsSection = document.getElementById('permissions-section');
    const permissionCheckboxes = document.querySelectorAll('.permission-check');
    
    if (role === 'owner') {
        permissionsSection.style.display = 'none';
        // Uncheck semua permissions untuk owner
        permissionCheckboxes.forEach(el => {
            el.checked = false;
            el.disabled = true;
        });
    } else {
        permissionsSection.style.display = 'block';
        permissionCheckboxes.forEach(el => el.disabled = false);
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Validasi form
document.getElementById('userForm').addEventListener('submit', function(e) {
    const role = document.getElementById('role').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    // Validasi password
    if (password.length < 8) {
        e.preventDefault();
        alert('Password harus minimal 8 karakter');
        return;
    }
    
    if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
        e.preventDefault();
        alert('Password harus mengandung huruf besar, huruf kecil, dan angka');
        return;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak cocok');
        return;
    }
    
    // Validasi permissions untuk non-owner
    if (role !== 'owner') {
        const permissions = document.querySelectorAll('.permission-check:checked');
        if (permissions.length === 0) {
            e.preventDefault();
            if (!confirm('User tidak memiliki permissions apapun. Lanjutkan?')) {
                return;
            }
        }
    }
});

// Trigger on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePermissions();
    
    // Auto-focus pada field pertama
    document.getElementById('name').focus();
});
</script>
@endsection