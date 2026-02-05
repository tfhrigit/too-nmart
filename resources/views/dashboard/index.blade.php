<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - Inventory Toko Bangunan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <div>
            <span class="text-muted">{{ now()->format('d F Y') }}</span>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #3498db;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Total Barang</h6>
                            <h2 class="mb-0">{{ $totalBarang }}</h2>
                            <small class="text-muted">Item berbeda</small>
                        </div>
                        <div class="stat-icon text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #e74c3c;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Stok Kritis</h6>
                            <h2 class="mb-0 text-danger">{{ $stokKritis }}</h2>
                            <small class="text-muted">Perlu restock</small>
                        </div>
                        <div class="stat-icon text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #2ecc71;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Total Penjualan</h6>
                            <h4 class="mb-0 text-success">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h4>
                            <small class="text-muted">Bulan ini</small>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="border-left-color: #f39c12;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Total Pembelian</h6>
                            <h4 class="mb-0 text-warning">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h4>
                            <small class="text-muted">Bulan ini</small>
                        </div>
                        <div class="stat-icon text-warning">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="row mb-4">
        <div class="col-12">
                <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-lightning"></i> Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
                        </a>
                        <a href="{{ route('barang-keluar.create') }}" class="btn btn-success">
                            <i class="bi bi-box-arrow-up"></i> Barang Keluar
                        </a>
                        <a href="{{ route('barang.create') }}" class="btn btn-info text-white">
                            <i class="bi bi-plus-circle"></i> Tambah Barang
                        </a>
                        <a href="{{ route('laporan.index') }}" class="btn btn-warning text-white">
                            <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Profit Chart -->
        <div class="row mb-4">
            <!-- Revenue & Expense Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-graph-up"></i> Grafik Pendapatan & Pengeluaran 6 Bulan
                            Terakhir</h5>
                        <canvas id="revenueExpenseChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pie Chart - Inventory Status -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-pie-chart"></i> Status Stok Barang</h5>
                        <div class="d-flex justify-content-center">
                            <canvas id="inventoryPieChart" width="200" height="200"></canvas>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="badge" style="background-color: #1e40af;">&nbsp;&nbsp;</span>
                            <small class="me-3">Barang Tersedia: {{ $totalBarang - $stokHabis }}</small>
                            <span class="badge" style="background-color: #60a5fa;">&nbsp;&nbsp;</span>
                            <small>Barang Habis: {{ $stokHabis }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Barang Terlaris -->
       <div class="row mb-4">

    <!-- TOP 5 BARANG TERLARIS -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-trophy"></i> Top 5 Barang Terlaris
                </h5>

                <div class="list-group list-group-flush">
                    @forelse($topBarang as $index => $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                <small>{{ Str::limit($item->barang->nama_barang, 25) }}</small>
                            </div>
                            <span class="badge bg-success">
                                {{ number_format($item->total_terjual, 0) }} {{ $item->barang->base_unit }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- CUSTOMER & SUPPLIER -->
    <div class="col-md-4 d-flex flex-column gap-3">

        <div class="card stat-card" style="border-left-color:#3498db;">
            <div class="card-body">
                <h6 class="text-muted">Customer</h6>
                <h2 class="text-primary mb-0">{{ $totalCustomer }}</h2>
                <small class="text-muted">Total pelanggan</small>
            </div>
        </div>

        <div class="card stat-card" style="border-left-color:#2ecc71;">
            <div class="card-body">
                <h6 class="text-muted">Supplier</h6>
                <h2 class="text-success mb-0">{{ $totalSupplier }}</h2>
                <small class="text-muted">Total pemasok</small>
            </div>
        </div>

    </div>
</div>


    <div class="row mb-4">
        <!-- Barang Stok Rendah -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-exclamation-circle text-danger"></i>
                        Barang Stok Rendah
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                    <th>Min</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangStokRendah as $barang)
                                    <tr>
                                        <td>{{ $barang->nama_barang }}</td>
                                        <td>{{ number_format($barang->stok_sekarang, 0) }} {{ $barang->base_unit }}</td>
                                        <td>{{ $barang->stok_minimum }}</td>
                                        <td>
                                            @if($barang->isStokHabis())
                                                <span class="badge bg-danger">Habis</span>
                                            @else
                                                <span class="badge badge-critical">Kritis</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="bi bi-check-circle text-success"></i> Semua stok aman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP 5 BARANG TIDAK LAKU 3 BULAN TERAKHIR (VERSI SEDERHANA) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-clock-history"></i>
                        Barang Tidak Laku (3 Bulan)
                    </h5>
                    @if($topBarangTidakLaku->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Stok</th>
                                        <th>Terakhir Jual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topBarangTidakLaku as $barang)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $barang->nama_barang }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $barang->kode_barang }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ number_format($barang->stok_sekarang, 0) }} {{ $barang->base_unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    > 3 bulan
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-6 text-success"></i>
                            <p class="mt-3 text-muted">Semua barang terjual dalam 3 bulan terakhir</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terakhir</h5>
                    <form action="{{ route('dashboard.clear-activities') }}" method="POST"
      onsubmit="return confirm('Hapus semua log aktivitas?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">
        <i class="bi bi-trash"></i> Bersihkan
    </button>
</form>

                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities->take(8) as $activity)
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between">
                                    <small>
                                        <i class="bi bi-person-circle"></i>
                                        <strong>{{ $activity->user->name }}</strong>
                                        {{ $activity->description }}
                                    </small>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3">Belum ada aktivitas</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-md-6">
            @if($notifications->count() > 0)
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title text-danger">
                            <i class="bi bi-bell-fill"></i> Notifikasi Penting
                        </h5>
                        @foreach($notifications as $notif)
                            <div class="alert alert-warning d-flex justify-content-between align-items-center mb-2" role="alert">
                                <div>
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ $notif->message }}
                                </div>
                                <a href="{{ route('barang.show', $notif->barang_id) }}" class="btn btn-sm btn-warning">
                                    Lihat Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .stat-card {
            border-left-width: 4px;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        .text-purple {
            color: #9b59b6;
        }

        .text-teal {
            color: #1abc9c;
        }

        .badge-critical {
            background-color: #f39c12;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
    </style>
@endpush



@push('scripts')
    <script>
        // Revenue vs Expense Chart
        const revChartLabels = {!! json_encode($chartData['labels']) !!};
        const revChartData = {!! json_encode($chartData['revenues']) !!};
        const expChartData = {!! json_encode($chartData['expenses']) !!};

        const revCtx = document.getElementById('revenueExpenseChart').getContext('2d');
        const revenueExpenseChart = new Chart(revCtx, {
            type: 'line',
            data: {
                labels: revChartLabels,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: revChartData,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: expChartData,
                        borderColor: '#f39c12',
                        backgroundColor: 'rgba(243, 156, 18, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                return label + new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        // Inventory Pie Chart
        const pieLabels = {!! json_encode($pieChartData['labels']) !!};
        const pieData = {!! json_encode($pieChartData['data']) !!};
        const pieColors = {!! json_encode($pieChartData['colors']) !!};

        const pieCtx = document.getElementById('inventoryPieChart').getContext('2d');
        const inventoryPieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: ${ctx.raw} barang`
                        }
                    }
                }
            }
        });
    </script>
@endpush