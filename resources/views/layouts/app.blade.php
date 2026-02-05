<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nmart-Build')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --light-blue: #dbeafe;
            --dark-blue: #1e3a8a;
            --danger-color: #dc2626;
            --success-color: #059669;
            --warning-color: #d97706;
            --info-color: #0c4a6e;
            --light-bg: #f8fafc;
            --dark-text: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 16.666666%;
            height: 100vh;
            background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
            box-shadow: 3px 0 15px rgba(30, 64, 175, 0.15);
            overflow-y: auto;
            z-index: 1000;
            border-right: 1px solid #dbeafe;
        }

        main {
            margin-left: 16.666666%;
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .sidebar-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1.5rem 1rem;
        }

        .sidebar-header h5 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
            margin-bottom: 0.25rem;
        }

        .sidebar-header small {
            font-size: 0.85rem;
            opacity: 0.9;
            color: #dbeafe;
            font-weight: 400;
        }

        .sidebar .nav-link {
            color: #e2e8f0;
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-left-color: #60a5fa;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-left-color: #3b82f6;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-section-title {
            padding: 0.5rem 1.5rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #93c5fd;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* NAVBAR STYLING - DIPERBAIKI SPACING */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: white !important;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }

        .navbar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0 1rem;
        }

        .navbar-brand {
            font-weight: 700;
            color: #1e40af !important;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .navbar-brand img {
            height: 32px;
            width: auto;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* User Profile Dropdown Styling */
        .user-profile-wrapper {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 0.5rem 0;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid #dbeafe;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            margin-right: 0.5rem;
        }

        .user-name {
            font-weight: 600;
            color: #1e40af;
            font-size: 0.95rem;
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.8rem;
            color: #64748b;
        }

        .user-role-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .user-role-badge.owner {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .user-role-badge.kasir {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .user-role-badge.staff_gudang {
            background-color: #d1fae5;
            color: #059669;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none !important;
            min-height: 40px;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
            color: white !important;
        }

        /* Dropdown Menu Styling */
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            padding: 0.5rem 0;
            min-width: 200px;
            border: 1px solid #e2e8f0;
            margin-top: 0.75rem !important;
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            color: #64748b;
            font-size: 0.85rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            color: #475569;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #1e40af;
        }

        .dropdown-divider {
            margin: 0.25rem 0;
            border-color: #e2e8f0;
        }

        /* Mobile Responsive */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #1e40af;
            font-size: 1.5rem;
            padding: 0.5rem;
        }

        /* Card Styling */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
            color: white !important;
            border: none;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
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
            transform: translateY(-1px);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
            border: none;
            font-weight: 600;
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);
            transform: translateY(-1px);
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
            transform: translateY(-1px);
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
            transform: translateY(-1px);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid #3b82f6;
            color: #3b82f6;
            font-weight: 600;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        /* Badge Styling */
        .badge {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
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
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
            color: white !important;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            color: white !important;
        }

        .badge-info {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%) !important;
            color: white !important;
        }

        /* Animations */
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
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease forwards;
            border-radius: 10px;
            background: white;
            border: 1px solid #e2e8f0;
        }

        .stat-card:hover {
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.1);
            transform: translateY(-3px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #3b82f6;
            opacity: 0.8;
        }

        /* Table Styling */
        .table {
            font-size: 0.95rem;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .table thead {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-bottom: 2px solid #3b82f6;
        }

        .table th {
            color: #1e40af;
            font-weight: 600;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 2px solid #bfdbfe;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
            box-shadow: inset 3px 0 0 #3b82f6;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            color: #475569;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0;
            padding: 1.25rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            background: #f8fafc;
        }

        /* Form Styling */
        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background-color: white;
            color: #334155;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background-color: white;
        }

        .form-label {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
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

        .alert-primary {
            background-color: #dbeafe;
            border-left-color: #1e40af;
            color: #1e40af;
        }

        /* Dropdown & Collapse Styling */
        .collapse .nav-link {
            padding-left: 2.5rem;
            font-size: 0.9rem;
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
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            main {
                margin-left: 0;
                padding-top: 1rem;
            }

            .navbar {
                padding: 0.75rem 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .user-profile-wrapper {
                gap: 1rem;
            }

            .user-info {
                display: none;
            }

            .logout-btn .btn-text {
                display: none;
            }

            .logout-btn {
                padding: 0.6rem;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        /* Content Area */
        .content-wrapper {
            padding: 0 1rem;
        }

        /* Main Content Area */
        .main-content {
            padding: 0 1rem 2rem 1rem;
        }

        /* Active Link Indicator */
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 50%;
            background-color: #3b82f6;
            border-radius: 0 2px 2px 0;
        }

        /* Section Dividers */
        .content-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        /* Custom Utility Classes */
        .text-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
        }

        .bg-gradient-blue-light {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
        }

        .border-blue {
            border-color: #3b82f6 !important;
        }

        .text-blue {
            color: #1e40af !important;
        }

        /* Dashboard Widgets */
        .dashboard-widget {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .dashboard-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .widget-title {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            font-size: 1.1rem;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        /* Pagination */
        .pagination .page-link {
            color: #1e40af;
            border: 1px solid #cbd5e1;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border-color: #3b82f6;
            color: white;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* DataTables Custom */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #64748b;
            font-size: 0.9rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }

        /* Loading Spinner */
        .loading-spinner {
            border: 3px solid #f1f5f9;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Tooltip Styling */
        .tooltip-inner {
            background-color: #1e40af;
            color: white;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .tooltip.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #1e40af;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
            margin-bottom: 1.5rem;
            border-radius: 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .breadcrumb-item {
            color: #64748b;
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: #1e40af;
            font-weight: 600;
        }

        /* Print Styles */
        @media print {
            .sidebar, .navbar {
                display: none !important;
            }
            
            main {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 col-lg-2 d-md-block sidebar p-0">
                <div class="sidebar-header text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <img src="{{ asset('img/logo3.png') }}" alt="Logo" style="height: 55px; width: auto;">
                    </div>
                    <h5 class="mb-0">Nmart-</h5>
                    <small>Build</small>
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
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 col-lg-10 ms-sm-auto">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid navbar-container">
                        <!-- Mobile Toggle Button -->
                        <button class="mobile-menu-toggle" type="button" onclick="toggleSidebar()">
                            <i class="bi bi-list"></i>
                        </button>

                        <!-- Logo/Brand -->
                        <a class="navbar-brand" href="{{ route('dashboard') }}">
                            <img src="{{ asset('img/logo3.png') }}" alt="Logo">
                            <span>Nmart-Build</span>
                        </a>

                        <!-- User Profile and Actions -->
                        <div class="user-profile-wrapper">
                            <!-- User Info Dropdown -->
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                                    href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="user-avatar">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="user-info d-none d-lg-block">
                                        <div class="user-name">{{ Auth::user()->name }}</div>
                                        <div class="user-role">
                                            <span class="user-role-badge {{ Auth::user()->role }}">
                                                {{ Auth::user()->role_name }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-header">
                                        <i class="bi bi-person-circle me-2"></i>
                                        {{ Auth::user()->name }}
                                    </li>
                                    <li class="dropdown-header">
                                        <small>Login sebagai {{ Auth::user()->role_name }}</small>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-person me-2"></i> Profil Saya
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-gear me-2"></i> Pengaturan
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Logout Button (Visible on Desktop) -->
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="logout-btn d-none d-md-flex">
                                <i class="bi bi-box-arrow-right"></i>
                                <span class="btn-text">Logout</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
                <div class="main-content">
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
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        // Add search filter for each column if needed
                    });
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Enhanced logout confirmation
        document.querySelectorAll('.logout-btn, .dropdown-item.text-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '{{ route('logout') }}') {
                    e.preventDefault();
                    if (confirm('Apakah Anda yakin ingin logout?')) {
                        document.getElementById('logout-form').submit();
                    }
                }
            });
        });
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
        /* Keep collapse menus always visible */
        .sidebar .collapse {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure interactive elements work properly */
        .sidebar,
        .sidebar * {
            pointer-events: auto !important;
        }

        /* Enhanced hover effects for blue theme */
        .btn-primary:hover, .btn-info:hover {
            filter: brightness(110%);
        }

        /* Form validation styling */
        .is-invalid {
            border-color: #dc2626 !important;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>

    <script>
        // Prevent sidebar hover conflicts
        document.querySelectorAll('.sidebar').forEach(sidebar => {
            sidebar.onmouseenter = null;
            sidebar.onmouseleave = null;
        });

        // Form validation enhancement
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid field
                    const firstInvalid = this.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });
        });
    </script>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

</body>
</html>