@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit User: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required onchange="togglePermissions()">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" 
                                    {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        <small class="form-text text-muted">
                            Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka
                        </small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation">
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                               placeholder="081234567890">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                User Aktif
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Centang untuk mengaktifkan user, unccentang untuk menonaktifkan
                        </small>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-3" id="permissions-section" style="display: none;">
                        <label class="form-label">Menu & Fitur yang Dapat Diakses</label>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Pilih menu dan fitur mana saja yang bisa diakses user ini. 
                                Jika tidak ada yang dipilih, user tidak bisa mengakses apapun.
                            </small>
                        </div>

                        <div class="row">
                            @php
                                $permissionGroups = [
                                    'Dashboard' => ['view_dashboard'],
                                    'Data Barang' => ['view_barang', 'create_barang', 'edit_barang', 'delete_barang'],
                                    'Barang Masuk' => ['view_barang_masuk', 'create_barang_masuk', 'edit_barang_masuk', 'delete_barang_masuk'],
                                    'Barang Keluar' => ['view_barang_keluar', 'create_barang_keluar', 'edit_barang_keluar', 'delete_barang_keluar'],
                                    'Supplier' => ['view_supplier', 'create_supplier', 'edit_supplier', 'delete_supplier'],
                                    'Customer' => ['view_customer', 'create_customer', 'edit_customer', 'delete_customer'],
                                    'Laporan' => ['view_laporan_transaksi', 'view_laporan_bulanan', 'view_laporan_pergerakan_barang', 'view_laporan_barang_tidak_laku'],
                                    'User Management' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
                                ];
                            @endphp

                            @foreach($permissionGroups as $group => $perms)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ $group }}</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($perms as $perm)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="{{ $perm }}" 
                                                   id="{{ $perm }}"
                                                   {{ in_array($perm, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $perm }}">
                                                {{ $permissions[$perm] }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Info Audit -->
                    <div class="alert alert-info mb-3">
                        <small>
                            <strong>Dibuat:</strong> {{ $user->created_at->format('d/m/Y H:i') }}<br>
                            <strong>Diubah:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">Informasi Role</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-danger">👑 Owner/Admin</h6>
                    <small>
                        Akses penuh ke SEMUA fitur sistem tanpa perlu memilih.
                    </small>
                </div>
                <div class="mb-3">
                    <h6 class="text-primary">💳 Kasir</h6>
                    <small>
                        Pilih menu & fitur spesifik yang ingin diberikan akses.
                    </small>
                </div>
                <div class="mb-3">
                    <h6 class="text-info">📦 Staff Gudang</h6>
                    <small>
                        Pilih menu & fitur spesifik yang ingin diberikan akses.
                    </small>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Aksi Berbahaya</h6>
            </div>
            <div class="card-body">
                @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user) }}" method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?\n\nTindakan ini tidak dapat dibatalkan!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Hapus User
                    </button>
                </form>
                @else
                <p class="text-muted small">Anda tidak dapat menghapus akun sendiri</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function togglePermissions() {
    const role = document.getElementById('role').value;
    const permissionsSection = document.getElementById('permissions-section');
    
    if (role === 'owner') {
        permissionsSection.style.display = 'none';
        // Uncheck semua permissions untuk owner
        document.querySelectorAll('.permission-check').forEach(el => el.checked = false);
    } else {
        permissionsSection.style.display = 'block';
    }
}

// Trigger on page load
document.addEventListener('DOMContentLoaded', togglePermissions);
</script>

@endsection
