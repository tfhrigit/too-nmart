<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    BarangController,
    BarangMasukController,
    BarangKeluarController,
    BarangMovementController,
    SupplierController,
    CustomerController,
    LaporanController,
    UserController
};

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';



/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', function () {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        Auth::logout();
        return redirect('/login');
    })->name('logout');



    Route::middleware(['auth'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ✅ TAMBAHKAN INI
        Route::delete('/dashboard/clear-activities', [DashboardController::class, 'clearActivities'])
            ->name('dashboard.clear-activities');

        // ... route lainnya
    });


    /* ==================== BARANG ==================== */
    Route::prefix('barang')->name('barang.')->group(function () {

        // 🔹 STATIC ROUTES HARUS DI ATAS
        Route::get('/', [BarangController::class, 'index'])->name('index');

        Route::middleware('role:owner,staff_gudang')->group(function () {
            Route::get('/create', [BarangController::class, 'create'])->name('create');
            Route::post('/', [BarangController::class, 'store'])->name('store');
            Route::post('/ajax-store', [BarangController::class, 'storeAjax'])->name('store.ajax');
        });

        // 🔹 DYNAMIC ROUTES PALING BAWAH
        Route::get('/{barang}', [BarangController::class, 'show'])->name('show');
        Route::get('/{barang}/units', [BarangController::class, 'getUnits'])->name('units');

        Route::middleware('role:owner,staff_gudang')->group(function () {
            Route::get('/{barang}/edit', [BarangController::class, 'edit'])->name('edit');
            Route::put('/{barang}', [BarangController::class, 'update'])->name('update');
            Route::delete('/{barang}', [BarangController::class, 'destroy'])->name('destroy');
        });
    });


    /* ==================== BARANG MASUK ==================== */
    Route::middleware(['auth', 'role:owner,staff_gudang'])
        ->prefix('barang-masuk')
        ->name('barang-masuk.')
        ->group(function () {

            // ================= AUTOCOMPLETE (STATIC - HARUS DI ATAS) =================
            Route::get('/autocomplete/barang', [BarangMasukController::class, 'autocompleteBarang'])
                ->name('autocomplete-barang');

            Route::get('/autocomplete/supplier', [BarangMasukController::class, 'autocompleteSupplier'])
                ->name('autocomplete-supplier');

            // ================= CRUD (MULTI-ITEM) =================
            // 1. INDEX - Tampilkan semua transaksi
            Route::get('/', [BarangMasukController::class, 'index'])->name('index');

            // 2. CREATE - Form tambah baru
            Route::get('/create', [BarangMasukController::class, 'create'])->name('create');

            // 3. STORE - Simpan transaksi baru
            Route::post('/', [BarangMasukController::class, 'store'])->name('store');

            // 4. SHOW DETAIL - Detail transaksi dengan no_transaksi
            Route::get('/detail/{noTransaksi}', [BarangMasukController::class, 'show'])
                ->name('show');

            // 5. EDIT - Form edit transaksi dengan no_transaksi
            Route::get('/edit/{noTransaksi}', [BarangMasukController::class, 'edit'])
                ->name('edit');

            // 6. UPDATE - Update transaksi dengan no_transaksi
            Route::put('/update/{noTransaksi}', [BarangMasukController::class, 'update'])
                ->name('update');

            // 7. DESTROY - Hapus transaksi dengan no_transaksi
            Route::delete('/delete/{noTransaksi}', [BarangMasukController::class, 'destroy'])
                ->name('destroy');

            // 8. PRINT - Cetak transaksi barang masuk
            Route::get('/print/{noTransaksi}', [BarangMasukController::class, 'print'])
                ->name('print');

            // routes/web.php (tambahkan route ini)
            Route::get('/barang-masuk/{noTransaksi}/get-data', [BarangMasukController::class, 'getTransactionData'])
                ->name('barang-masuk.get-data')
                ->middleware('auth');

            Route::resource('barang-masuk', BarangMasukController::class);
        });

    /* ==================== BARANG KELUAR ==================== */
    Route::middleware('role:owner,kasir')
        ->prefix('barang-keluar')
        ->name('barang-keluar.')
        ->group(function () {

            // Autocomplete routes
            Route::get(
                '/autocomplete-barang',
                [BarangKeluarController::class, 'autocompleteBarang']
            )->name('autocomplete-barang');

            Route::get(
                '/autocomplete-customer',
                [BarangKeluarController::class, 'autocompleteCustomer']
            )->name('autocomplete-customer');

            // Custom routes (harus didefinisikan SEBELUM resource)
            Route::get(
                '/{barangKeluar}/print',
                [BarangKeluarController::class, 'print']
            )->name('print');

            // Resource routes - gunakan nama khusus untuk parameter
            Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
            Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
            Route::post('/', [BarangKeluarController::class, 'store'])->name('store');
            Route::get('/{barangKeluar}', [BarangKeluarController::class, 'show'])->name('show');
            Route::get('/{barangKeluar}/edit', [BarangKeluarController::class, 'edit'])->name('edit');
            Route::put('/{barangKeluar}', [BarangKeluarController::class, 'update'])->name('update');
            Route::delete('/{barangKeluar}', [BarangKeluarController::class, 'destroy'])->name('destroy');
        });


    /* ==================== SUPPLIER ==================== */
    Route::middleware('role:owner,staff_gudang')->resource('supplier', SupplierController::class);

    /* ==================== CUSTOMER ==================== */
    Route::middleware('role:owner,kasir')->resource('customer', CustomerController::class);

    /* ==================== LAPORAN ==================== */
    Route::prefix('laporan')->name('laporan.')->group(function () {

        /* ===== OWNER ONLY ===== */
        Route::middleware('role:owner')->group(function () {

            Route::get('/', [LaporanController::class, 'index'])
                ->name('index');

            Route::get('/bulanan', [LaporanController::class, 'laporanBulanan'])
                ->name('bulanan');

            Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])
                ->name('export.pdf'); // <-- Perbaiki ini

            Route::get('/bulanan/export-pdf', [LaporanController::class, 'exportBulananPdf'])
                ->name('bulanan.export-pdf');

            /* ===== DETAIL LAPORAN ===== */
            Route::get('/barang-masuk', [LaporanController::class, 'barangMasuk'])
                ->name('barang-masuk');

            Route::get('/barang-keluar', [LaporanController::class, 'barangKeluar'])
                ->name('barang-keluar');
        });

        /* ===== OWNER & STAFF GUDANG ===== */
        Route::middleware('role:owner,staff_gudang')->group(function () {
            Route::get('/barang', [LaporanController::class, 'laporanBarang'])
                ->name('barang');

            Route::get('/tidak-laku', [LaporanController::class, 'barangTidakLaku'])
                ->name('tidak_laku');
        });
    });

    /* ==================== USERS ==================== */
    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    /* ==================== PERGERAKAN BARANG ==================== */
    Route::middleware('role:owner,staff_gudang')
        ->prefix('barang-movement')
        ->name('barang-movement.')
        ->group(function () {
            Route::get('/', [BarangMovementController::class, 'index'])->name('index');
            Route::get('/riwayat-stok', [BarangMovementController::class, 'riwayatStok'])->name('riwayat-stok');
            Route::get('/{id}', [BarangMovementController::class, 'show'])->name('show');
        });
});
