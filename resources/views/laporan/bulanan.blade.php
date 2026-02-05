@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="mb-1"><i class="bi bi-graph-up"></i> Laporan Rekapan Bulanan</h1>
            <p class="text-muted mb-0">Analisis Penjualan & Pembelian Periode {{ $tahun }}</p>
        </div>
        <div>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Laporan Transaksi
            </a>
            <a href="{{ route('laporan.bulanan.export-pdf', ['tahun' => $tahun]) }}" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card mb-5 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('laporan.bulanan') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Pilih Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @foreach($tahunList as $year)
                                <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><i class="bi bi-basket"></i> Total Penjualan</p>
                            <h4 class="mb-0 text-success fw-bold">
                                Rp {{ number_format($totals['penjualan'], 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem; opacity: 0.2;">
                            <i class="bi bi-cash-flow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><i class="bi bi-box"></i> Total Pembelian</p>
                            <h4 class="mb-0 text-danger fw-bold">
                                Rp {{ number_format($totals['pembelian'], 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="text-danger" style="font-size: 2.5rem; opacity: 0.2;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><i class="bi bi-award"></i> Total Profit</p>
                            <h4 class="mb-0 {{ $totals['profit'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                Rp {{ number_format($totals['profit'], 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="{{ $totals['profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 2.5rem; opacity: 0.2;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><i class="bi bi-percent"></i> Margin Profit</p>
                            <h4 class="mb-0 {{ $totals['pembelian'] > 0 && ($totals['profit'] / $totals['pembelian']) * 100 >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $totals['pembelian'] > 0 ? number_format(($totals['profit'] / $totals['pembelian']) * 100, 2) : 0 }}%
                            </h4>
                        </div>
                        <div class="text-info" style="font-size: 2.5rem; opacity: 0.2;">
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Grafik Bulanan -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4"><i class="bi bi-graph-up"></i> Grafik Penjualan & Pembelian {{ $tahun }}</h5>
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Metode Pembayaran -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4"><i class="bi bi-credit-card"></i> Metode Pembayaran</h5>
                    <canvas id="paymentChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4"><i class="bi bi-award"></i> Profit {{ $tahun }}</h5>
                    <canvas id="profitChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapan Bulanan -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-4"><i class="bi bi-table"></i> Detail Rekapan Bulanan {{ $tahun }}</h5>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="fw-bold" style="border-top: 2px solid #dee2e6;">Bulan</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">Pembelian</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">Penjualan</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">Cash</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">QRIS</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">Transfer</th>
                            <th class="text-center fw-bold" style="border-top: 2px solid #dee2e6;">Transaksi</th>
                            <th class="text-end fw-bold" style="border-top: 2px solid #dee2e6;">Profit</th>
                            <th class="text-center fw-bold" style="border-top: 2px solid #dee2e6;">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyReports as $report)
                            <tr>
                                <td class="fw-bold">{{ $report->bulan_name }}</td>
                                <td class="text-end">Rp {{ number_format($report->total_pembelian, 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($report->total_penjualan, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <span class="badge bg-success">Rp
                                        {{ number_format($report->total_penjualan_cash, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-primary">Rp
                                        {{ number_format($report->total_penjualan_qris, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info">Rp
                                        {{ number_format($report->total_penjualan_transfer, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">{{ $report->jumlah_transaksi_masuk }} → {{ $report->jumlah_transaksi_keluar }}</small>
                                </td>
                                <td class="text-end {{ $report->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>Rp {{ number_format($report->profit, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $report->profit_percentage >= 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ number_format($report->profit_percentage, 2) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light" style="border-top: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                        <tr>
                            <th class="fw-bold" style="color: #495057;">TOTAL</th>
                            <th class="text-end fw-bold" style="color: #495057;">Rp {{ number_format($totals['pembelian'], 0, ',', '.') }}</th>
                            <th class="text-end fw-bold text-success" style="color: #28a745;">Rp {{ number_format($totals['penjualan'], 0, ',', '.') }}</th>
                            <th class="text-end fw-bold" style="color: #495057;">Rp {{ number_format($totals['cash'], 0, ',', '.') }}</th>
                            <th class="text-end fw-bold" style="color: #495057;">Rp {{ number_format($totals['qris'], 0, ',', '.') }}</th>
                            <th class="text-end fw-bold" style="color: #495057;">Rp {{ number_format($totals['transfer'], 0, ',', '.') }}</th>
                            <th class="fw-bold" style="color: #495057;"></th>
                            <th class="text-end {{ $totals['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <strong style="font-size: 1.1rem;">Rp {{ number_format($totals['profit'], 0, ',', '.') }}</strong>
                            </th>
                            <th class="text-center">
                                <span class="badge {{ $totals['profit'] >= 0 ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.95rem;">
                                    {{ $totals['pembelian'] > 0 ? number_format(($totals['profit'] / $totals['pembelian']) * 100, 2) : 0 }}%
                                </span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15) !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .card-title {
        color: #2c3e50;
        font-size: 1.05rem;
    }

    h1 {
        color: #2c3e50;
        font-weight: 600;
    }

    table {
        font-size: 0.95rem;
    }

    tbody tr:hover {
        background-color: #f5f7fa;
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        const chartData = {!! json_encode($chartData) !!};

        // Chart Color Schemes
        const colors = {
            primary: '#0d6efd',
            success: '#198754',
            danger: '#dc3545',
            info: '#0dcaf0',
            warning: '#ffc107',
        };

        // Monthly Sales Chart
        const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Pembelian',
                        data: chartData.pembelian,
                        borderColor: colors.danger,
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: colors.danger,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Penjualan',
                        data: chartData.penjualan,
                        borderColor: colors.success,
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: colors.success,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: 'bold' },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // Payment Method Chart
        const ctxPayment = document.getElementById('paymentChart').getContext('2d');
        new Chart(ctxPayment, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Cash',
                        data: chartData.cash,
                        backgroundColor: colors.success,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'QRIS',
                        data: chartData.qris,
                        backgroundColor: colors.primary,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Transfer',
                        data: chartData.transfer,
                        backgroundColor: colors.info,
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: 'bold' },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // Profit Chart
        const ctxProfit = document.getElementById('profitChart').getContext('2d');
        new Chart(ctxProfit, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Profit',
                    data: chartData.profit,
                    backgroundColor: chartData.profit.map(v => v >= 0 ? colors.success : colors.danger),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: 'bold' },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return 'Profit: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    </script>
@endpush