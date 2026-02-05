<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PT PUTRA JAYA SAMPANGAN - Sistem Inventory')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #db2777;
            --secondary-color: #ec4899;
            --accent-color: #f472b6;
            --danger-color: #ef4444;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --pink-light: #fdf2f8;
            --pink-dark: #be185d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fdf2f8;
            color: #701a75;
        }

        /* Sidebar Styling */
        .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 16.666666%; /* col-md-2 */
    height: 100vh;
    background: linear-gradient(180deg, #f472b6 0%, #db2777 100%);
    box-shadow: 3px 0 15px rgba(219, 39, 119, 0.15);
    overflow-y: auto;
    z-index: 1000;
}
main {
    margin-left: 16.666666%;
    padding-top: 1rem;
}



        .sidebar-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .sidebar-header h5 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
        }

        .sidebar-header small {
            font-size: 0.85rem;
            opacity: 0.9;
            display: block;
            margin-top: 0.25rem;
            color: #fce7f3;
        }

        .sidebar .nav-link {
            color: #fdf2f8;
            padding: 12px 20px;
            margin: 8px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: #ffffff;
            border-left: 4px solid #ffffff;
            padding-left: 16px;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.3);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .sidebar-section-title {
            padding: 0 20px;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fbcfe8;
        }

        /* Navbar Styling */
        .navbar {
    position: relative;
    z-index: 1100; /* LEBIH TINGGI DARI SIDEBAR */
    box-shadow: 0 2px 12px rgba(219, 39, 119, 0.1);
    background: linear-gradient(135deg, #ffffff 0%, #fdf2f8 100%) !important;
    border-bottom: 3px solid #ec4899;
}

        .navbar-brand {
            font-weight: 700;
            color: #db2777 !important;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(219, 39, 119, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(219, 39, 119, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;
            color: white !important;
            border: none;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            transform: translateY(-2px);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            font-weight: 600;
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            font-weight: 600;
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
            transform: translateY(-2px);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            font-weight: 600;
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            color: white;
        }

        .btn-pink {
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
            border: none;
            font-weight: 600;
            color: white;
        }

        .btn-pink:hover {
            background: linear-gradient(135deg, #f9a8d4 0%, #f472b6 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 114, 182, 0.4);
            color: white;
        }

        /* Badge Styling */
        .badge {
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-critical,
        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
            animation: pulse 2s infinite;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
        }

        .badge-primary {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;
            color: white !important;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important;
            color: white !important;
        }

        .badge-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            color: white !important;
        }

        .badge-pink {
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%) !important;
            color: white !important;
        }

        /* Animations */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stat Cards */
        .stat-card {
            border-left: 4px solid #ec4899;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease forwards;
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .stat-card:hover {
            box-shadow: 0 8px 25px rgba(219, 39, 119, 0.15);
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #f472b6;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            opacity: 0.8;
            transform: scale(1.1);
        }

        /* Table Styling */
        .table {
            font-size: 0.95rem;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
            border-top: 2px solid #ec4899;
            border-bottom: 2px solid #ec4899;
        }

        .table th {
            color: #db2777;
            font-weight: 700;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 2px solid #fbcfe8;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #fdf2f8;
        }

        .table tbody tr:hover {
            background-color: #fdf2f8;
            box-shadow: inset 3px 0 0 #ec4899;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(219, 39, 119, 0.2);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            border: none;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-footer {
            border-top: 1px solid #fce7f3;
            padding: 1.5rem;
        }

        /* Form Styling */
        .form-control, .form-select {
            border: 1px solid #fbcfe8;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background-color: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
            background-color: #fdf2f8;
        }

        .form-label {
            font-weight: 600;
            color: #831843;
            margin-bottom: 0.5rem;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
            padding: 1rem 1.25rem;
        }

        .alert-info {
            background-color: #dbeafe;
            border-left-color: #3b82f6;
            color: #1e40af;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-left-color: #f59e0b;
            color: #92400e;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        .alert-success {
            background-color: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }

        .alert-pink {
            background-color: #fce7f3;
            border-left-color: #ec4899;
            color: #831843;
        }

        /* Dropdown & Collapse Styling */
        .dropdown-menu {
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.15);
            padding: 0.5rem 0;
            background: white;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: #831843;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #fdf2f8;
            color: #db2777;
            padding-left: 2rem;
        }

        /* Collapse Menu Styling */
        .nav-link.collapse-trigger {
            transition: all 0.3s ease;
        }

        .nav-link.collapse-trigger[aria-expanded="true"] {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-link.collapse-trigger .bi-chevron-down {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .nav-link.collapse-trigger[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        .collapse {
            animation: slideDown 0.3s ease;
        }

        .collapse:not(.show) {
            display: none;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #fce7f3;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #f9a8d4 0%, #f472b6 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -250px;
                width: 250px;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .stat-card {
                animation: none;
            }
        }

        /* Content Area */
        main {
            background-color: #fdf2f8;
            min-height: 100vh;
        }

        /* Active Link Indicator */
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background-color: #ffffff;
            border-radius: 0 3px 3px 0;
        }

        /* Section Dividers */
        .content-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(219, 39, 119, 0.05);
            border: 1px solid #fce7f3;
        }

        /* Custom Utility Classes */
        .text-gradient-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;
        }

        .bg-gradient-pink-light {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%) !important;
        }

        .border-pink {
            border-color: #ec4899 !important;
        }

        .text-pink {
            color: #db2777 !important;
        }

        /* Dashboard Widgets */
        .dashboard-widget {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #fce7f3;
            transition: all 0.3s ease;
        }

        .dashboard-widget:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(219, 39, 119, 0.1);
        }

        .widget-title {
            color: #db2777;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #fce7f3;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #fce7f3;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 col-lg-2 d-md-block sidebar p-0">
                
                    <div class="sidebar-header text-center py-3">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <img src="{{ asset('img/logo3.png') }}" alt="Logo" style="height: 55px; width: auto;">
                        </div>
                        <h5 class="mb-0">
                            PUTRA JAYA
                        </h5>
                        <small>SAMPANGAN</small>
                    </div>

                    <nav class="sidebar-nav px-2 pt-3">

                        <!-- Dashboard -->
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Data Barang -->
                        @permission('view_barang')
                        <button class="nav-link collapse-trigger w-100 text-start d-flex align-items-center {{ request()->routeIs('barang.*') ? 'active' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#barangMenu"
                            aria-expanded="false">
                            <i class="bi bi-boxes me-2"></i>
                            <span class="flex-grow-1">Barang</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="collapse" id="barangMenu">
                            <div class="ps-3">
                                <a class="nav-link {{ request()->routeIs('barang.index') ? 'active' : '' }}"
   href="{{ route('barang.index') }}">
    <i class="bi bi-box-seam"></i>
    Data Barang
</a>

                                <a class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}"
                                    href="{{ route('supplier.index') }}">
                                    <i class="bi bi-truck"></i> Supplier
                                </a>
                                <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}"
                                    href="{{ route('customer.index') }}">
                                    <i class="bi bi-people"></i> Pelanggan
                                </a>
                            </div>
                        </div>
                        @endpermission

                        <!-- TRANSAKSI Section -->
                        <div class="sidebar-section-title mt-3 mb-2">
                            <small>Transaksi</small>
                        </div>

                        <!-- Barang Masuk -->
                        @permission('view_barang_masuk')
                        <a class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}"
                            href="{{ route('barang-masuk.index') }}">
                            <i class="bi bi-box-arrow-in-down"></i>
                            <span>Barang Masuk</span>
                        </a>
                        @endpermission

                        <!-- Barang Keluar -->
                        @permission('view_barang_keluar')
                        <a class="nav-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}"
                            href="{{ route('barang-keluar.index') }}">
                            <i class="bi bi-box-arrow-up"></i>
                            <span>Barang Keluar</span>
                        </a>
                        @endpermission

                        <!-- LAPORAN Section -->
                        <div class="sidebar-section-title mt-3 mb-2">
                            <small>Laporan</small>
                        </div>

                        <!-- Laporan -->
                        @role('owner')
                        <button class="nav-link collapse-trigger w-100 text-start d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#laporanMenu"
                            aria-expanded="false">

                            <i class="bi bi-file-earmark-text me-2"></i>
                            <span class="flex-grow-1">Laporan</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <div class="collapse" id="laporanMenu">
                            <div class="ps-3">
                                <a class="nav-link d-flex align-items-center gap-2"
   href="{{ route('laporan.index') }}">
    <i class="bi bi-receipt"></i>
    <span>Transaksi</span>
</a>

<a class="nav-link d-flex align-items-center gap-2"
   href="{{ route('laporan.bulanan') }}">
    <i class="bi bi-calendar-month"></i>
    <span>Bulanan</span>
</a>

                                <a class="nav-link" href="{{ route('barang-movement.index') }}">
                                    <i class="bi bi-graph-up"></i> Pergerakan Bulanan
                                </a>
                                <a class="nav-link" href="{{ route('barang-movement.riwayat-stok') }}">
                                    <i class="bi bi-table"></i> Riwayat Stok Harian
                                </a>
                               <a class="nav-link d-flex align-items-center gap-2"
   href="{{ route('laporan.tidak_laku') }}">
    <i class="bi bi-box2-heart"></i>
    <span>Barang Tidak Laku</span>
</a>
                            </div>
                        </div>
                        @endrole

                        <!-- Laporan (Staff Gudang) -->
                        @role('staff_gudang')
                        <button class="nav-link collapse-trigger w-100 text-start d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#laporanMenuStaffGudang"
                            aria-expanded="false">

                            <i class="bi bi-file-earmark-text me-2"></i>
                            <span class="flex-grow-1">Laporan</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <div class="collapse" id="laporanMenuStaffGudang">
                            <div class="ps-3">
                                <a class="nav-link" href="{{ route('barang-movement.index') }}">
                                    <i class="bi bi-graph-up"></i> Pergerakan Bulanan
                                </a>
                                <a class="nav-link" href="{{ route('barang-movement.riwayat-stok') }}">
                                    <i class="bi bi-table"></i> Riwayat Stok Harian
                                </a>
                                <a class="nav-link" href="{{ route('laporan.tidak_laku') }}">Barang Tidak Laku</a>
                            </div>
                        </div>
                        @endrole

                        <!-- PENGATURAN Section -->
                        <div class="sidebar-section-title mt-3 mb-2">
                            <small>Pengaturan</small>
                        </div>

                        <!-- User Management -->
                        @permission('users.index')
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Manajemen User</span>
                        </a>
                        @endpermission

                    </nav>
                </div>
            </nav>
            <!-- Main Content -->
            <main class="col-md-10 col-lg-10 ms-sm-auto px-md-4">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
               href="#" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">

                <i class="bi bi-person-circle"
                   style="font-size: 1.6rem; color: #db2777;"></i>

                <div class="text-start d-none d-md-block">
                    <div style="font-weight:600; color:#831843;">
                        {{ Auth::user()->name }}
                    </div>
                    <small>
                        <span class="{{ Auth::user()->role_badge }}">
                            {{ Auth::user()->role_name }}
                        </span>
                    </small>
                </div>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header">
                    Login sebagai <b>{{ Auth::user()->role_name }}</b>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</div>


                        
    

        <li>
            <a class="dropdown-item text-danger"
               href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</li>


                    </div>
                </nav>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.collapse').forEach(collapse => {
            collapse.addEventListener('hide.bs.collapse', function (e) {
                if (collapse.matches(':hover')) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app-custom.js') }}"></script>

    <script>
        // Initialize DataTables
        $(document).ready(function () {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                dom: 'rtip',
                lengthChange: false,
                responsive: true
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

    @stack('scripts')

    @push('scripts')
        <script>
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => {
                el.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });
            
            function markAsRead(notificationId) {
                fetch(`/dashboard/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => location.reload());
            }

            function markAllAsRead() {
                fetch('/dashboard/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => location.reload());
            }

            function deleteNotification(notificationId) {
                if (confirm('Hapus notifikasi ini?')) {
                    fetch(`/dashboard/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(() => location.reload());
                }
            }
        </script>
    @endpush

    <style>
        /* MATIKAN AUTO HIDE MENU */
        .sidebar .collapse {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* MATIKAN EFEK HOVER */
        .sidebar:hover .collapse {
            display: block !important;
        }

        /* JAGA AREA INTERAKSI */
        .sidebar,
        .sidebar * {
            pointer-events: auto !important;
        }

        /* Custom hover effect for pink theme */
        .btn-primary:hover, .btn-pink:hover {
            filter: brightness(110%);
        }
    </style>

    <script>
        document.querySelectorAll('.sidebar').forEach(sidebar => {
            sidebar.onmouseenter = null;
            sidebar.onmouseleave = null;
        });
    </script>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

</body>

</html>