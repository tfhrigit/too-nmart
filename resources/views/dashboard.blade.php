<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    <i class="bi bi-speedometer2 mr-2"></i>Dashboard
                </h2>
            </div>
            <div>
                <small class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</small>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Dashboard Minimalis -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="bg-white/10 p-3 rounded-xl mr-4">
                            <i class="bi bi-building text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Nmart-Build</h1>
                            <p class="text-gray-300 text-sm">Sistem Manajemen Inventory Terintegrasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Barang -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Barang</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ \App\Models\Barang::count() }}
                            </p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i class="bi bi-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stok Kritis -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Stok Kritis</p>
                            <p class="text-2xl font-bold text-red-600">
                                @php
                                    $stokKritis = \App\Models\Barang::whereColumn('stok_sekarang', '<=', 'stok_minimum')->count();
                                @endphp
                                {{ $stokKritis }}
                            </p>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Hari Ini -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Transaksi Hari Ini</p>
                            <p class="text-2xl font-bold text-green-600">
                                @php
                                    $transaksiHariIni = \App\Models\BarangKeluar::whereDate('tanggal', today())->count();
                                @endphp
                                {{ $transaksiHariIni }}
                            </p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <i class="bi bi-arrow-up-right text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Customer -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Customer</p>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ \App\Models\Customer::count() }}
                            </p>
                        </div>
                        <div class="bg-indigo-50 p-3 rounded-lg">
                            <i class="bi bi-people text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Quick Actions -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Akses Cepat</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="{{ route('barang.index') }}" 
                               class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                <i class="bi bi-box text-2xl text-blue-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Data Barang</span>
                            </a>
                            <a href="{{ route('barang-masuk.index') }}" 
                               class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                <i class="bi bi-box-arrow-in-down text-2xl text-green-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Barang Masuk</span>
                            </a>
                            <a href="{{ route('barang-keluar.index') }}" 
                               class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                <i class="bi bi-box-arrow-up text-2xl text-orange-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Barang Keluar</span>
                            </a>
                            <a href="{{ route('barang-movement.riwayat-stok') }}" 
                               class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                <i class="bi bi-graph-up text-2xl text-purple-600 mb-2"></i>
                                <span class="text-sm font-medium text-gray-700">Riwayat Stok</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Supplier Info -->
                <div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 h-full">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pemasok</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                        <i class="bi bi-building text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Total Supplier</p>
                                        <p class="text-xs text-gray-500">Pemasok aktif</p>
                                    </div>
                                </div>
                                <span class="text-xl font-bold text-gray-900">
                                    {{ \App\Models\Supplier::count() }}
                                </span>
                            </div>
                            
                            <!-- Informasi tambahan bisa ditambahkan di sini -->
                            <div class="mt-6">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                    <i class="bi bi-plus-circle mr-2"></i>
                                    Tambah Supplier Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 5 Barang Terlaris -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Barang Terlaris</h3>
                    <span class="text-sm text-gray-500">Bulan Ini</span>
                </div>
                
                <!-- Placeholder untuk chart/table -->
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="bi bi-bar-chart text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 mb-2">Belum ada data penjualan</p>
                    <p class="text-sm text-gray-400">Data akan muncul setelah ada transaksi</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom minimalis styling */
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .rounded-xl {
            border-radius: 12px;
        }
        
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        
        .border-gray-100 {
            border-color: #f3f4f6;
        }
        
        /* Hover effects */
        .hover\:shadow-md:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Text colors for better contrast */
        .text-gray-900 {
            color: #111827;
        }
        
        .text-gray-700 {
            color: #374151;
        }
        
        .text-gray-500 {
            color: #6b7280;
        }
        
        .text-gray-400 {
            color: #9ca3af;
        }
    </style>

    <script>
        // Simple script untuk interaksi
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to cards
            const cards = document.querySelectorAll('.bg-white.rounded-xl');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.classList.add('shadow-md');
                    card.classList.remove('shadow-sm');
                });
                
                card.addEventListener('mouseleave', () => {
                    card.classList.remove('shadow-md');
                    card.classList.add('shadow-sm');
                });
            });
        });
    </script>
</x-app-layout>