<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\StockHistory;
use App\Models\Activity;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangMasukTransaksi;
use App\Models\BarangMasukItem;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        // Query dengan filter
        $query = BarangMasukTransaksi::with(['supplier', 'items.barang', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_akhir
            ]);
        }

        // Filter supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q2) use ($search) {
                        $q2->where('nama_supplier', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.barang', function ($q3) use ($search) {
                        $q3->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('kode_barang', 'like', "%{$search}%");
                    });
            });
        }

        // Pagination
        $transaksis = $query->paginate(10);

        // Hanya ambil suppliers untuk dropdown filter
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('barang-masuk.index', compact('transaksis', 'suppliers'));
    }

    public function print($noTransaksi)
    {
        $transaksi = BarangMasukTransaksi::with(['supplier', 'items.barang', 'user'])
            ->where('no_transaksi', $noTransaksi)
            ->firstOrFail();

        return view('barang-masuk.print', compact('transaksi'));
    }

    public function create()
    {
        // Tidak perlu lagi menggunakan BarangMasuk::count()
        // Cukup gunakan count dari transaksi hari ini
        $lastNumber = BarangMasukTransaksi::whereDate('created_at', today())->count();

        // Ambil semua satuan yang pernah digunakan
        $existingUnits = Barang::select('base_unit')
            ->distinct()
            ->pluck('base_unit')
            ->toArray();

        $existingItemUnits = \App\Models\ItemUnit::select('unit_name')
            ->distinct()
            ->pluck('unit_name')
            ->toArray();

        $allUnits = array_unique(array_merge($existingUnits, $existingItemUnits));

        // Fallback jika AJAX gagal
        $suppliers = Supplier::limit(10)->get();
        $barangs = Barang::with('itemUnits')->limit(10)->get();

        return view('barang-masuk.create', compact(
            'lastNumber',
            'suppliers',
            'barangs',
            'allUnits'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'invoice_supplier' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'barang_items' => 'required|array|min:1',
            'barang_items.*.mode' => 'required|in:existing,manual',
            'barang_items.*.jumlah' => 'required|numeric|min:0.01',
            'barang_items.*.unit_name' => 'required|string',
            'barang_items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            /* =====================================================
             * 1️⃣ NO TRANSAKSI (HEADER)
             * ===================================================== */
            $noTransaksi = $this->generateGuaranteedUniqueNoTransaksi();

            /* =====================================================
             * 2️⃣ SUPPLIER
             * ===================================================== */
            if ($request->supplier_mode === 'manual') {
                $supplier = Supplier::create([
                    'kode_supplier' => Supplier::generateKode(),
                    'nama_supplier' => $request->supplier_manual,
                    'alamat' => 'Belum diisi',
                    'no_hp' => $request->supplier_telp ?? '-',
                ]);
                $supplierId = $supplier->id;
            } else {
                $supplierId = $request->supplier_id;
            }

            /* =====================================================
             * 3️⃣ CREATE HEADER TRANSAKSI (1x SAJA)
             * ===================================================== */
            $transaksi = BarangMasukTransaksi::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'supplier_id' => $supplierId,
                'invoice_supplier' => $request->invoice_supplier,
                'keterangan' => $request->keterangan,
                'grand_total' => 0,
                'user_id' => auth()->id(),
            ]);

            $grandTotal = 0;

            /* =====================================================
             * 4️⃣ LOOP ITEM (DETAIL)
             * ===================================================== */
            foreach ($request->barang_items as $item) {

                /* ---------- BARANG ---------- */
                if ($item['mode'] === 'manual') {
                    $barang = Barang::firstOrCreate(
                        ['nama_barang' => $item['nama_manual']],
                        [
                            'kode_barang' => Barang::generateKode(),
                            'base_unit' => $item['satuan_manual'],
                            'stok_sekarang' => 0,
                            'stok_minimum' => $item['stok_minimum'] ?? 10,
                            'deskripsi' => 'Input dari barang masuk',
                        ]
                    );

                    $barang->itemUnits()->firstOrCreate([
                        'unit_name' => $item['satuan_manual'],
                    ], [
                        'multiplier' => 1,
                        'is_base' => true,
                    ]);
                } else {
                    $barang = Barang::with('itemUnits')->findOrFail($item['barang_id']);
                }

                /* ---------- HITUNG ---------- */
                $jumlah = (float) $item['jumlah'];
                $harga = (float) $item['harga_beli'];
                $total = $jumlah * $harga;
                $grandTotal += $total;

                /* ---------- KONVERSI STOK ---------- */
                $jumlahBase = $jumlah;
                if ($item['unit_name'] !== $barang->base_unit) {
                    $jumlahBase = $barang->convertToBaseUnit($jumlah, $item['unit_name']);
                }

                /* =====================================================
                 * 5️⃣ INSERT DETAIL
                 * ===================================================== */
                $detail = BarangMasukItem::create([
                    'barang_masuk_transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'unit_name' => $item['unit_name'],
                    'harga_beli' => $harga,
                    'total_harga' => $total,
                ]);

                /* ---------- UPDATE STOK ---------- */
                $stokAwal = $barang->stok_sekarang;
                $barang->stok_sekarang += $jumlahBase;
                $barang->save();

                /* ---------- STOCK HISTORY ---------- */
                StockHistory::create([
                    'tanggal' => $request->tanggal,
                    'barang_id' => $barang->id,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $jumlahBase,
                    'stok_sebelum' => $stokAwal,
                    'stok_sesudah' => $barang->stok_sekarang,
                    'referensi_tabel' => 'barang_masuk_items',
                    'referensi_id' => $detail->id,
                    'keterangan' => "Barang masuk ({$noTransaksi})",
                ]);
            }

            /* =====================================================
             * 6️⃣ UPDATE GRAND TOTAL HEADER
             * ===================================================== */
            $transaksi->update([
                'grand_total' => $grandTotal
            ]);

            DB::commit();

            return redirect()
                ->route('barang-masuk.index')
                ->with('success', 'Barang masuk berhasil disimpan')
                ->with('no_transaksi', $noTransaksi);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    private function generateGuaranteedUniqueNoTransaksi()
    {
        $date = date('Ymd');
        $time = date('His');

        $microtime = microtime(true);
        $microParts = explode('.', (string) $microtime);
        $microSecond = isset($microParts[1]) ? substr($microParts[1], 0, 6) : '000000';

        $random = bin2hex(random_bytes(3));

        return "BM-{$date}-{$time}-{$microSecond}-{$random}";
    }

    // Method untuk menampilkan detail transaksi berdasarkan no_transaksi
    public function show($noTransaksi)
    {
        // Debug: cek apakah method dipanggil
        \Log::info('BarangMasukController@show called with noTransaksi: ' . $noTransaksi);

        try {
            $transaksi = BarangMasukTransaksi::with(['supplier', 'items.barang', 'user'])
                ->where('no_transaksi', $noTransaksi)
                ->firstOrFail();

            \Log::info('Transaksi found: ' . $transaksi->no_transaksi);

            return view('barang-masuk.show', compact('transaksi'));
        } catch (\Exception $e) {
            \Log::error('Error in BarangMasukController@show: ' . $e->getMessage());
            abort(404, 'Transaksi tidak ditemukan');
        }
    }

    public function edit($noTransaksi)
    {
        $transaksi = BarangMasukTransaksi::with('items.barang')
            ->where('no_transaksi', $noTransaksi)
            ->firstOrFail();

        $barangs = Barang::all();
        $suppliers = Supplier::all();

        // Ambil semua satuan yang pernah digunakan
        $existingUnits = Barang::select('base_unit')
            ->distinct()
            ->pluck('base_unit')
            ->toArray();

        $existingItemUnits = \App\Models\ItemUnit::select('unit_name')
            ->distinct()
            ->pluck('unit_name')
            ->toArray();

        $allUnits = array_unique(array_merge($existingUnits, $existingItemUnits));

        return view('barang-masuk.edit', compact(
            'transaksi',
            'barangs',
            'suppliers',
            'allUnits'
        ));
    }

    public function update(Request $request, $noTransaksi)
    {
        // Hapus transaksi lama dan buat baru (lebih aman untuk multi-item)
        DB::beginTransaction();

        try {
            $transaksi = BarangMasukTransaksi::where('no_transaksi', $noTransaksi)
                ->firstOrFail();

            // Hapus semua item lama dan revert stock
            foreach ($transaksi->items as $item) {
                if ($item->barang) {
                    $barang = $item->barang;

                    // Konversi ke base unit
                    $jumlahDalamBaseUnit = $item->jumlah;
                    if ($item->unit_name !== $barang->base_unit) {
                        try {
                            $jumlahDalamBaseUnit = $barang->convertToBaseUnit($item->jumlah, $item->unit_name);
                        } catch (\Exception $e) {
                            // Jika unit tidak ditemukan, tetap menggunakan jumlah asli
                        }
                    }

                    // Revert stock
                    $barang->stok_sekarang -= $jumlahDalamBaseUnit;
                    $barang->save();

                    // Hapus stock history lama
                    StockHistory::where('referensi_tabel', 'barang_masuk_items')
                        ->where('referensi_id', $item->id)
                        ->delete();
                }
            }

            // Hapus semua item lama
            $transaksi->items()->delete();

            // Validasi input baru
            $request->validate([
                'tanggal' => 'required|date',
                'invoice_supplier' => 'nullable|string|max:100',
                'keterangan' => 'nullable|string',
                'barang_items' => 'required|array|min:1',
                'barang_items.*.mode' => 'required|in:existing,manual',
                'barang_items.*.jumlah' => 'required|numeric|min:0.01',
                'barang_items.*.unit_name' => 'required|string',
                'barang_items.*.harga_beli' => 'required|numeric|min:0',
            ]);

            /* =====================================================
             * UPDATE SUPPLIER
             * ===================================================== */
            if ($request->supplier_mode === 'manual') {
                $supplier = Supplier::create([
                    'kode_supplier' => Supplier::generateKode(),
                    'nama_supplier' => $request->supplier_manual,
                    'alamat' => 'Belum diisi',
                    'no_hp' => $request->supplier_telp ?? '-',
                ]);
                $supplierId = $supplier->id;
            } else {
                $supplierId = $request->supplier_id;
            }

            /* =====================================================
             * UPDATE HEADER TRANSAKSI
             * ===================================================== */
            $transaksi->update([
                'tanggal' => $request->tanggal,
                'supplier_id' => $supplierId,
                'invoice_supplier' => $request->invoice_supplier,
                'keterangan' => $request->keterangan,
                'grand_total' => 0,
            ]);

            $grandTotal = 0;

            /* =====================================================
             * LOOP ITEM BARU (DETAIL)
             * ===================================================== */
            foreach ($request->barang_items as $item) {

                /* ---------- BARANG ---------- */
                if ($item['mode'] === 'manual') {
                    $barang = Barang::firstOrCreate(
                        ['nama_barang' => $item['nama_manual']],
                        [
                            'kode_barang' => Barang::generateKode(),
                            'base_unit' => $item['satuan_manual'],
                            'stok_sekarang' => 0,
                            'stok_minimum' => $item['stok_minimum'] ?? 10,
                            'deskripsi' => 'Input dari barang masuk (update)',
                        ]
                    );

                    $barang->itemUnits()->firstOrCreate([
                        'unit_name' => $item['satuan_manual'],
                    ], [
                        'multiplier' => 1,
                        'is_base' => true,
                    ]);
                } else {
                    $barang = Barang::with('itemUnits')->findOrFail($item['barang_id']);
                }

                /* ---------- HITUNG ---------- */
                $jumlah = (float) $item['jumlah'];
                $harga = (float) $item['harga_beli'];
                $total = $jumlah * $harga;
                $grandTotal += $total;

                /* ---------- KONVERSI STOK ---------- */
                $jumlahBase = $jumlah;
                if ($item['unit_name'] !== $barang->base_unit) {
                    $jumlahBase = $barang->convertToBaseUnit($jumlah, $item['unit_name']);
                }

                /* =====================================================
                 * INSERT DETAIL BARU
                 * ===================================================== */
                $detail = BarangMasukItem::create([
                    'barang_masuk_transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'unit_name' => $item['unit_name'],
                    'harga_beli' => $harga,
                    'total_harga' => $total,
                ]);

                /* ---------- UPDATE STOK ---------- */
                $stokAwal = $barang->stok_sekarang;
                $barang->stok_sekarang += $jumlahBase;
                $barang->save();

                /* ---------- STOCK HISTORY ---------- */
                StockHistory::create([
                    'tanggal' => $request->tanggal,
                    'barang_id' => $barang->id,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $jumlahBase,
                    'stok_sebelum' => $stokAwal,
                    'stok_sesudah' => $barang->stok_sekarang,
                    'referensi_tabel' => 'barang_masuk_items',
                    'referensi_id' => $detail->id,
                    'keterangan' => "Update barang masuk ({$noTransaksi})",
                ]);
            }

            /* =====================================================
             * UPDATE GRAND TOTAL HEADER
             * ===================================================== */
            $transaksi->update([
                'grand_total' => $grandTotal
            ]);

            DB::commit();

            return redirect()->route('barang-masuk.index')
                ->with('success', 'Transaksi barang masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($noTransaksi)
    {
        DB::beginTransaction();

        try {
            $transaksi = BarangMasukTransaksi::where('no_transaksi', $noTransaksi)
                ->firstOrFail();

            // Revert stock for each item
            foreach ($transaksi->items as $item) {
                if ($item->barang) {
                    $barang = $item->barang;

                    // Konversi ke base unit
                    $jumlahDalamBaseUnit = $item->jumlah;
                    if ($item->unit_name !== $barang->base_unit) {
                        try {
                            $jumlahDalamBaseUnit = $barang->convertToBaseUnit($item->jumlah, $item->unit_name);
                        } catch (\Exception $e) {
                            // Jika unit tidak ditemukan, tetap menggunakan jumlah asli
                        }
                    }

                    // Revert stock
                    $oldStock = $barang->stok_sekarang;
                    $barang->stok_sekarang -= $jumlahDalamBaseUnit;
                    $barang->save();

                    // Create stock history untuk penghapusan
                    StockHistory::create([
                        'tanggal' => now()->format('Y-m-d'),
                        'barang_id' => $barang->id,
                        'jenis_transaksi' => 'keluar',
                        'jumlah' => $jumlahDalamBaseUnit,
                        'stok_sebelum' => $oldStock,
                        'stok_sesudah' => $barang->stok_sekarang,
                        'referensi_tabel' => 'barang_masuk_items',
                        'referensi_id' => $item->id,
                        'keterangan' => 'Penghapusan transaksi barang masuk no. ' . $noTransaksi,
                    ]);

                    // Check if stock becomes critical after deletion
                    if ($barang->isStokKritis()) {
                        $notificationType = $barang->isStokHabis() ? 'stok_habis' : 'stok_kritis';
                        $message = $barang->isStokHabis()
                            ? "Stok {$barang->nama_barang} habis!"
                            : "Stok {$barang->nama_barang} mencapai batas minimum!";

                        Notification::create([
                            'barang_id' => $barang->id,
                            'type' => $notificationType,
                            'message' => $message,
                            'is_read' => false,
                        ]);
                    }

                    // Delete the original stock history for this item
                    StockHistory::where('referensi_tabel', 'barang_masuk_items')
                        ->where('referensi_id', $item->id)
                        ->where('jenis_transaksi', 'masuk')
                        ->delete();
                }
            }

            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'model_type' => 'BarangMasukTransaksi',
                'model_id' => $transaksi->id,
                'description' => "Menghapus transaksi barang masuk: {$noTransaksi}",
            ]);

            // Delete the transaction (items will be cascade deleted)
            $transaksi->delete();

            DB::commit();

            return redirect()->route('barang-masuk.index')
                ->with('success', 'Transaksi barang masuk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showTransaksi($noTransaksi)
    {
        $transaksi = BarangMasukTransaksi::with(['items.barang', 'supplier', 'user'])
            ->where('no_transaksi', $noTransaksi)
            ->firstOrFail();

        return view('barang-masuk.show-transaksi', compact('transaksi'));
    }

    // Method autocomplete untuk AJAX
    public function autocompleteBarang(Request $request)
    {
        $search = $request->get('q', '');

        try {
            $query = Barang::with('itemUnits');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'LIKE', "%{$search}%")
                        ->orWhere('kode_barang', 'LIKE', "%{$search}%");
                });
            }

            $barangs = $query->orderBy('nama_barang')
                ->limit(20)
                ->get()
                ->map(function ($barang) {
                    $stokStatus = 'bg-success';
                    if ($barang->stok_sekarang <= 0) {
                        $stokStatus = 'bg-danger';
                    } elseif ($barang->stok_sekarang <= $barang->stok_minimum) {
                        $stokStatus = 'bg-warning';
                    }

                    return [
                        'id' => $barang->id,
                        'text' => $barang->nama_barang . ' (' . $barang->kode_barang . ')',
                        'nama' => $barang->nama_barang,
                        'kode' => $barang->kode_barang,
                        'stok' => $barang->stok_sekarang,
                        'satuan' => $barang->base_unit,
                        'stok_minimum' => $barang->stok_minimum,
                        'stok_status' => $stokStatus,
                        'units' => $barang->itemUnits->map(function ($unit) {
                            return [
                                'unit_name' => $unit->unit_name,
                                'multiplier' => $unit->multiplier,
                                'is_base' => $unit->is_base
                            ];
                        })
                        ,
                        'harga_beli' => $barang->harga_beli,
                    ];
                });

            return response()->json(['results' => $barangs->toArray()]);
        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function autocompleteSupplier(Request $request)
    {
        $search = $request->get('q', '');

        try {
            $query = Supplier::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_supplier', 'LIKE', "%{$search}%")
                        ->orWhere('kode_supplier', 'LIKE', "%{$search}%")
                        ->orWhere('no_hp', 'LIKE', "%{$search}%")
                        ->orWhere('alamat', 'LIKE', "%{$search}%");
                });
            }

            $suppliers = $query->orderBy('nama_supplier')
                ->limit(20)
                ->get()
                ->map(function ($supplier) {
                    return [
                        'id' => $supplier->id,
                        'text' => $supplier->nama_supplier . ' (' . $supplier->kode_supplier . ')',
                        'nama' => $supplier->nama_supplier,
                        'kode' => $supplier->kode_supplier,
                        'alamat' => $supplier->alamat,
                        'telp' => $supplier->no_hp
                    ];
                });

            return response()->json(['results' => $suppliers->toArray()]);
        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    // Method untuk generate nomor transaksi (tidak digunakan, sudah ada generateGuaranteedUniqueNoTransaksi)
    public static function generateNoTransaksi()
    {
        $today = date('Ymd');
        $lastTransaksi = BarangMasukTransaksi::where('no_transaksi', 'like', "BM-{$today}-%")
            ->orderBy('no_transaksi', 'desc')
            ->first();

        $lastNumber = $lastTransaksi ?
            (int) substr($lastTransaksi->no_transaksi, -4) : 0;

        return 'BM-' . $today . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}