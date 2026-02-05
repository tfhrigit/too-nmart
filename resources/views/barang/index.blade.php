@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Data Barang</h2>
    <div>
        <a href="{{ route('barang.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('barang.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <select name="filter" class="form-select">
                        <option value="">Semua Barang</option>
                        <option value="kritis" {{ request('filter') == 'kritis' ? 'selected' : '' }}>
                            Stok Kritis
                        </option>
                        <option value="kosong" {{ request('filter') == 'kosong' ? 'selected' : '' }}>
                            Barang Kosong (Stok = 0)
                        </option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Stok Sekarang</th>
                        <th>Satuan Dasar</th>
                        <th>Satuan Lain</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Min Stock</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $barang)
                    <tr class="{{ $barang->isStokKritis() ? 'table-warning' : '' }}">
                        <td><strong>{{ $barang->kode_barang }}</strong></td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>
                            <strong>{{ number_format($barang->stok_sekarang, 2) }}</strong>
                            {{ $barang->base_unit }}
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $barang->base_unit }}</span>
                        </td>
                        <td>
                            @foreach($barang->itemUnits->where('is_base', false) as $unit)
                                <span class="badge bg-secondary me-1">
                                    {{ $unit->unit_name }} ({{ $unit->multiplier }} {{ $barang->base_unit }})
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @if($barang->harga_beli && $barang->harga_beli > 0)
                                Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($barang->harga_jual && $barang->harga_jual > 0)
                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $barang->stok_minimum }}</td>
                        <td>
                            @if($barang->isStokHabis())
                                <span class="badge bg-danger">Habis</span>
                            @elseif($barang->isStokKritis())
                                <span class="badge bg-warning text-dark">Kritis</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('barang.edit', $barang) }}" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-hapus" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $barang->id }}"
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-kode="{{ $barang->kode_barang }}"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">Tidak ada data barang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-end">
            {{ $barangs->links() }}
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-3">Apakah Anda yakin ingin menghapus barang ini?</h5>
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-info-circle"></i>
                    <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Kode Barang</strong></td>
                                <td id="modalKodeBarang">-</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Barang</strong></td>
                                <td id="modalNamaBarang">-</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Saat Ini</strong></td>
                                <td id="modalStokBarang">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="confirmationText" class="form-label">
                            Ketik <strong>"HAPUS"</strong> untuk konfirmasi
                        </label>
                        <input type="text" class="form-control" id="confirmationText" 
                               placeholder="Ketik HAPUS" required>
                        <div class="form-text">Ini untuk memastikan Anda tidak melakukan kesalahan.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete" disabled>
                    <i class="bi bi-trash"></i> Hapus Barang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="bi bi-check-circle"></i> Berhasil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-3">Barang berhasil dihapus!</h5>
                <p id="successMessage">Data barang telah dihapus dari sistem.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-hapus:hover {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
    
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        border-bottom: none;
        padding: 1.5rem 1.5rem 0;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: none;
        padding: 0 1.5rem 1.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        cursor: pointer;
    }
    
    /* Style untuk pagination Bootstrap */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        color: #0d6efd;
        border: 1px solid #dee2e6;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .page-link:hover {
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
    // Inisialisasi modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    
    // Variable untuk menyimpan data barang yang akan dihapus
    let barangToDelete = null;
    let deleteUrl = '';
    
    // Ketika tombol hapus diklik
    document.querySelectorAll('.btn-hapus').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const kode = this.getAttribute('data-kode');
            
            // Set data ke modal
            document.getElementById('modalKodeBarang').textContent = kode;
            document.getElementById('modalNamaBarang').textContent = nama;
            
            // Ambil stok dari baris tabel
            const row = this.closest('tr');
            const stok = row.querySelector('td:nth-child(3)').textContent.trim();
            document.getElementById('modalStokBarang').textContent = stok;
            
            // Set URL untuk delete form
            deleteUrl = "{{ route('barang.destroy', ':id') }}".replace(':id', id);
            document.getElementById('deleteForm').action = deleteUrl;
            
            // Reset form konfirmasi
            document.getElementById('confirmationText').value = '';
            document.getElementById('confirmDelete').disabled = true;
            
            // Simpan data barang
            barangToDelete = { id, nama, kode };
            
            // Tampilkan modal
            deleteModal.show();
        });
    });
    
    // Validasi input konfirmasi
    document.getElementById('confirmationText').addEventListener('input', function() {
        const confirmButton = document.getElementById('confirmDelete');
        if (this.value.toUpperCase() === 'HAPUS') {
            confirmButton.disabled = false;
        } else {
            confirmButton.disabled = true;
        }
    });
    
    // Tombol konfirmasi hapus
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (barangToDelete) {
            // Submit form
            document.getElementById('deleteForm').submit();
            
            // Tampilkan modal sukses
            setTimeout(() => {
                deleteModal.hide();
                successModal.show();
            }, 500);
        }
    });
    
    // Auto-focus pada input konfirmasi saat modal terbuka
    document.getElementById('deleteModal').addEventListener('shown.bs.modal', function () {
        document.getElementById('confirmationText').focus();
    });
    
    // Reset form saat modal ditutup
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('confirmationText').value = '';
        document.getElementById('confirmDelete').disabled = true;
        barangToDelete = null;
    });
</script>
@endpush