<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="bi bi-speedometer2"></i> Dashboard - PT PUTRA JAYA SAMPANGAN
                </h2>
            </div>
            <div>
                <small class="text-muted">Selamat datang, {{ auth()->user()->name }}!</small>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Professional Dashboard Header -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-lg shadow-lg p-8 text-white">
                    <h3 class="text-4xl font-bold mb-2">
                        <i class="bi bi-building text-warning"></i> PT PUTRA JAYA SAMPANGAN
                    </h3>
                    <p class="text-slate-200 text-lg">Sistem Manajemen Inventory</p>
                    <p class="text-slate-300 text-sm mt-2">Kelola stok dan laporan penjualan dengan mudah dan efisien</p>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Barang -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(30, 60, 114, 0.1); transition: all 0.3s;">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Barang</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2" style="color: #1e3c72;">
                                @php
                                    $totalBarang = \App\Models\Barang::count();
                                @endphp
                                {{ $totalBarang }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">Produk terdaftar</p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3" style="background-color: rgba(30, 60, 114, 0.1);">
                            <i class="bi bi-box text-2xl text-blue-600" style="color: #1e3c72; font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Stok Kritis -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500 hover:shadow-lg transition" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(30, 60, 114, 0.1); transition: all 0.3s;">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Stok Kritis</p>
                            <p class="text-3xl font-bold text-red-600 mt-2" style="color: #e74c3c;">
                                @php
                                    $stokKritis = \App\Models\Barang::whereColumn('stok_sekarang', '<=', 'stok_minimum')->count();
                                @endphp
                                {{ $stokKritis }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">Perlu pemesanan</p>
                        </div>
                        <div class="bg-red-100 rounded-lg p-3" style="background-color: rgba(231, 76, 60, 0.1);">
                            <i class="bi bi-exclamation-triangle text-2xl text-red-600" style="color: #e74c3c; font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Hari Ini -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(30, 60, 114, 0.1); transition: all 0.3s;">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Transaksi Hari Ini</p>
                            <p class="text-3xl font-bold text-green-600 mt-2" style="color: #27ae60;">
                                @php
                                    $transaksiHariIni = \App\Models\BarangKeluar::whereDate('tanggal', today())->count();
                                @endphp
                                {{ $transaksiHariIni }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">Barang keluar</p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3" style="background-color: rgba(39, 174, 96, 0.1);">
                            <i class="bi bi-arrow-up text-2xl text-green-600" style="color: #27ae60; font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>

                

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="bi bi-lightning"></i> Akses Cepat
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('barang.index') }}" class="text-center p-4 border rounded-lg hover:bg-gray-50 transition">
                        <i class="bi bi-box text-3xl text-blue-600"></i>
                        <p class="mt-2 text-sm font-medium">Data Barang</p>
                    </a>
                    <a href="{{ route('barang-masuk.index') }}" class="text-center p-4 border rounded-lg hover:bg-gray-50 transition">
                        <i class="bi bi-box-arrow-in-down text-3xl text-green-600"></i>
                        <p class="mt-2 text-sm font-medium">Barang Masuk</p>
                    </a>
                    <a href="{{ route('barang-keluar.index') }}" class="text-center p-4 border rounded-lg hover:bg-gray-50 transition">
                        <i class="bi bi-box-arrow-up text-3xl text-orange-600"></i>
                        <p class="mt-2 text-sm font-medium">Barang Keluar</p>
                    </a>
                    <a href="{{ route('barang-movement.riwayat-stok') }}" class="text-center p-4 border rounded-lg hover:bg-gray-50 transition">
                        <i class="bi bi-graph-up text-3xl text-purple-600"></i>
                        <p class="mt-2 text-sm font-medium">Riwayat Stok</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Statistik Tambahan -->
<div class="mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KIRI : Top 5 Barang Terlaris -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="bi bi-trophy text-warning"></i> Top 5 Barang Terlaris
                </h3>

                <p class="text-sm text-gray-500">Belum ada data</p>
                {{-- nanti bisa diganti tabel --}}
            </div>
        </div>

        <!-- KANAN : Customer & Supplier -->
        <div class="lg:col-span-4 space-y-4">

            <!-- Customer -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Customer</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            {{ \App\Models\Customer::count() }}
                        </p>
                        <small class="text-gray-500">Total pelanggan</small>
                    </div>
                    <i class="bi bi-people text-3xl text-blue-500"></i>
                </div>
            </div>

            <!-- Supplier -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Supplier</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ \App\Models\Supplier::count() }}
                        </p>
                        <small class="text-gray-500">Total pemasok</small>
                    </div>
                    <i class="bi bi-building text-3xl text-green-500"></i>
                </div>
            </div>

        </div>
    </div>
</div>


    <style>
        .grid {
            display: grid;
        }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        @media (min-width: 768px) {
            .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        .gap-6 { gap: 1.5rem; }
        .gap-4 { gap: 1rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .text-center { text-align: center; }
        .text-white { color: white; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-sm { font-size: 0.875rem; }
        .text-3xl { font-size: 1.875rem; }
        .text-lg { font-size: 1.125rem; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .border { border: 1px solid #e5e7eb; }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-white { background-color: white; }
        .rounded { border-radius: 0.375rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .shadow-md { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .shadow-lg { box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        .hover\:bg-gray-50:hover { background-color: #f9fafb; }
        .transition { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
    </style>

    <script>
        // Delete single notification
        document.querySelectorAll('.delete-notification').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const notifId = this.getAttribute('data-id');
                const item = document.querySelector(`.notification-item[data-id="${notifId}"]`);
                
                // Hapus dari UI
                item.remove();
                
                // Optional: Send AJAX request to mark as read
                fetch(`/notifications/${notifId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
            });
        });
    </script>
</x-app-layout>
