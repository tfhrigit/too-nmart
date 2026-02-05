<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\StockHistory;
use App\Models\Activity;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['customer', 'user', 'items.barang']);

        // Filter by date
        if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        // Filter by metode pembayaran
        if ($request->has('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('items.barang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('nama_customer', 'like', "%{$search}%");
                    });
            });
        }

        $barangKeluars = $query->latest('tanggal')->paginate(20);

        return view('barang_keluar.index', compact('barangKeluars'));
    }

    public function create()
    {
        $barangs = Barang::with('itemUnits')
            ->where('stok_sekarang', '>', 0)
            ->get();

        $customers = Customer::all();
        $lastNumber = BarangKeluar::whereDate('created_at', today())->count();

        return view('barang_keluar.create', compact('barangs', 'customers', 'lastNumber'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validasi data utama
            $request->validate([
                'tanggal' => 'required|date',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_manual' => 'nullable|string|min:3',
                'customer_hp' => 'nullable|string',
                'metode_pembayaran' => 'required|in:cash,qris,transfer',
                'keterangan' => 'nullable|string',
            ]);

            // Validasi items
            $items = $request->input('barang_items', []);
            if (empty($items)) {
                throw new \Exception('Minimal satu barang harus ditambahkan');
            }

            foreach ($items as $index => $item) {
                $mode = $item['mode'] ?? 'existing';

                if ($mode === 'existing') {
                    // Untuk barang existing, validasi barang_id
                    if (!isset($item['barang_id']) || empty($item['barang_id'])) {
                        throw new \Exception("Barang ke-" . ($index + 1) . ": ID barang harus diisi untuk barang yang sudah ada");
                    }
                } else {
                    // Untuk barang manual, validasi nama_manual
                    if (!isset($item['nama_manual']) || empty(trim($item['nama_manual']))) {
                        throw new \Exception("Barang ke-" . ($index + 1) . ": Nama barang manual harus diisi");
                    }
                }

                // Validasi umum untuk semua mode
                if (!isset($item['jumlah']) || $item['jumlah'] <= 0) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Jumlah harus lebih dari 0");
                }

                if (!isset($item['unit_name']) || empty($item['unit_name'])) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Satuan harus dipilih");
                }

                if (!isset($item['harga_jual']) || $item['harga_jual'] <= 0) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Harga jual harus lebih dari 0");
                }
            }

            // Handle customer
            $customerId = null;
            if ($request->customer_mode === 'manual' && $request->customer_manual) {
                // Bersihkan nomor HP dari strip jika ada
                $cleanedPhone = preg_replace('/[^0-9]/', '', $request->customer_hp);

                $customer = Customer::create([
                    'kode_customer' => Customer::generateKode(),
                    'nama_customer' => $request->customer_manual,
                    'no_hp' => $cleanedPhone,
                    'alamat' => 'Belum diisi',
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $request->customer_id;
            }

            // Create transaksi utama
            $barangKeluar = BarangKeluar::create([
                'no_transaksi' => BarangKeluar::generateNoTransaksi(),
                'tanggal' => $request->tanggal,
                'customer_id' => $customerId,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan ?? '',
                'user_id' => auth()->id(),
            ]);

            // Proses setiap item
            foreach ($items as $item) {
                $mode = $item['mode'] ?? 'existing';

                if ($mode === 'existing') {
                    // =========== BARANG EXISTING ===========
                    $barang = Barang::findOrFail($item['barang_id']);

                    // Convert to base unit
                    $jumlahBaseUnit = $barang->convertToBaseUnit($item['jumlah'], $item['unit_name']);

                    if (!$jumlahBaseUnit || $jumlahBaseUnit <= 0) {
                        throw new \Exception("Konversi ke base unit gagal untuk barang: {$barang->nama_barang}");
                    }

                    // Check stock availability
                    if ($barang->stok_sekarang < $jumlahBaseUnit) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi! Stok tersedia: {$barang->stok_sekarang} {$barang->base_unit}");
                    }

                    // Calculate total harga
                    $totalHarga = $item['jumlah'] * $item['harga_jual'];

                    // Create item
                    $barangKeluarItem = BarangKeluarItem::create([
                        'barang_keluar_id' => $barangKeluar->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'unit_name' => $item['unit_name'],
                        'jumlah_in_base_unit' => $jumlahBaseUnit,
                        'harga_jual' => $item['harga_jual'],
                        'total_harga' => $totalHarga,
                    ]);

                    // Update stock
                    $stokSebelum = $barang->stok_sekarang;
                    $barang->stok_sekarang -= $jumlahBaseUnit;
                    $barang->save();

                    // Create stock history
                    StockHistory::create([
                        'tanggal' => $request->tanggal,
                        'barang_id' => $barang->id,
                        'jenis_transaksi' => 'keluar',
                        'jumlah' => $jumlahBaseUnit,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $barang->stok_sekarang,
                        'referensi_tabel' => 'barang_keluar_items',
                        'referensi_id' => $barangKeluarItem->id,
                        'keterangan' => "Barang keluar - Transaksi: {$barangKeluar->no_transaksi}",
                    ]);

                    // Create notification if stock is critical
                    if ($barang->isStokKritis()) {
                        Notification::firstOrCreate([
                            'barang_id' => $barang->id,
                            'type' => $barang->isStokHabis() ? 'stok_habis' : 'stok_kritis',
                            'is_read' => false,
                        ], [
                            'message' => $barang->isStokHabis()
                                ? "Stok {$barang->nama_barang} habis!"
                                : "Stok {$barang->nama_barang} mencapai batas minimum ({$barang->stok_sekarang} {$barang->base_unit})!",
                        ]);
                    }
                } else {
                    // =========== BARANG MANUAL ===========
                    $namaBarangManual = $item['nama_manual'] ?? 'Barang Manual';

                    // Calculate total harga
                    $totalHarga = $item['jumlah'] * $item['harga_jual'];

                    // Create item dengan barang_id NULL (karena ini barang manual)
                    $barangKeluarItem = BarangKeluarItem::create([
                        'barang_keluar_id' => $barangKeluar->id,
                        'barang_id' => null, // Barang manual tidak punya barang_id
                        'nama_barang_manual' => $namaBarangManual, // Simpan nama barang manual
                        'jumlah' => $item['jumlah'],
                        'unit_name' => $item['unit_name'],
                        'jumlah_in_base_unit' => $item['jumlah'], // Untuk manual, jumlah_in_base_unit sama dengan jumlah
                        'harga_jual' => $item['harga_jual'],
                        'total_harga' => $totalHarga,
                    ]);
                    // Tidak perlu update stok untuk barang manual
                }
            }

            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'model_type' => 'BarangKeluar',
                'model_id' => $barangKeluar->id,
                'description' => "Menambahkan barang keluar multi-item: {$barangKeluar->no_transaksi} dengan " . count($items) . " item",
            ]);

            DB::commit();

            return redirect()->route('barang-keluar.index')
                ->with('success', 'Transaksi barang keluar berhasil ditambahkan')
                ->with('no_transaksi', $barangKeluar->no_transaksi);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating Barang Keluar:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load(['customer', 'user', 'items' => function ($query) {
            $query->with(['barang.itemUnits']);
        }]);

        return view('barang_keluar.show', compact('barangKeluar'));
    }

    public function edit(BarangKeluar $barangKeluar)
    {
        $barangs = Barang::with('itemUnits')
            ->where('stok_sekarang', '>', 0)
            ->get();

        $customers = Customer::all();
        $barangKeluar->load(['items' => function ($query) {
            $query->with(['barang.itemUnits']);
        }]);

        return view('barang_keluar.edit', compact('barangKeluar', 'barangs', 'customers'));
    }

    public function update(Request $request, BarangKeluar $barangKeluar)
    {
        DB::beginTransaction();

        try {
            // Validasi data utama
            $request->validate([
                'tanggal' => 'required|date',
                'customer_id' => 'nullable|exists:customers,id',
                'metode_pembayaran' => 'required|in:cash,qris,transfer',
                'keterangan' => 'nullable|string',
            ]);

            // Validasi items
            $items = $request->input('barang_items', []);
            if (empty($items)) {
                throw new \Exception('Minimal satu barang harus ditambahkan');
            }

            foreach ($items as $index => $item) {
                $mode = $item['mode'] ?? 'existing';

                if ($mode === 'existing') {
                    if (!isset($item['barang_id']) || empty($item['barang_id'])) {
                        throw new \Exception("Barang ke-" . ($index + 1) . ": ID barang harus diisi untuk barang yang sudah ada");
                    }
                } else {
                    if (!isset($item['nama_manual']) || empty(trim($item['nama_manual']))) {
                        throw new \Exception("Barang ke-" . ($index + 1) . ": Nama barang manual harus diisi");
                    }
                }

                if (!isset($item['jumlah']) || $item['jumlah'] <= 0) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Jumlah harus lebih dari 0");
                }

                if (!isset($item['unit_name']) || empty($item['unit_name'])) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Satuan harus dipilih");
                }

                if (!isset($item['harga_jual']) || $item['harga_jual'] <= 0) {
                    throw new \Exception("Barang ke-" . ($index + 1) . ": Harga jual harus lebih dari 0");
                }
            }

            // 1. Kembalikan semua stok dari transaksi lama
            foreach ($barangKeluar->items as $oldItem) {
                if ($oldItem->barang_id) {
                    $barang = $oldItem->barang;
                    $barang->stok_sekarang += $oldItem->jumlah_in_base_unit;
                    $barang->save();

                    // Hapus stock history lama
                    StockHistory::where('referensi_tabel', 'barang_keluar_items')
                        ->where('referensi_id', $oldItem->id)
                        ->delete();
                }
            }

            // 2. Hapus semua items lama
            $barangKeluar->items()->delete();

            // 3. Update transaksi utama
            $barangKeluar->update([
                'tanggal' => $request->tanggal,
                'customer_id' => $request->customer_id,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan ?? '',
            ]);

            // 4. Tambahkan items baru
            foreach ($items as $item) {
                $mode = $item['mode'] ?? 'existing';

                if ($mode === 'existing') {
                    $barang = Barang::findOrFail($item['barang_id']);

                    // Convert to base unit
                    $jumlahBaseUnit = $barang->convertToBaseUnit($item['jumlah'], $item['unit_name']);

                    if (!$jumlahBaseUnit || $jumlahBaseUnit <= 0) {
                        throw new \Exception("Konversi ke base unit gagal untuk barang: {$barang->nama_barang}");
                    }

                    // Check stock availability
                    if ($barang->stok_sekarang < $jumlahBaseUnit) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi! Stok tersedia: {$barang->stok_sekarang} {$barang->base_unit}");
                    }

                    // Calculate total harga
                    $totalHarga = $item['jumlah'] * $item['harga_jual'];

                    // Create item
                    $barangKeluarItem = BarangKeluarItem::create([
                        'barang_keluar_id' => $barangKeluar->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'unit_name' => $item['unit_name'],
                        'jumlah_in_base_unit' => $jumlahBaseUnit,
                        'harga_jual' => $item['harga_jual'],
                        'total_harga' => $totalHarga,
                    ]);

                    // Update stock (kurangi karena sudah dikembalikan sebelumnya)
                    $stokSebelum = $barang->stok_sekarang;
                    $barang->stok_sekarang -= $jumlahBaseUnit;
                    $barang->save();

                    // Create stock history
                    StockHistory::create([
                        'tanggal' => $request->tanggal,
                        'barang_id' => $barang->id,
                        'jenis_transaksi' => 'keluar',
                        'jumlah' => $jumlahBaseUnit,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $barang->stok_sekarang,
                        'referensi_tabel' => 'barang_keluar_items',
                        'referensi_id' => $barangKeluarItem->id,
                        'keterangan' => "Barang keluar - Transaksi: {$barangKeluar->no_transaksi}",
                    ]);

                    // Create notification if stock is critical
                    if ($barang->isStokKritis()) {
                        Notification::firstOrCreate([
                            'barang_id' => $barang->id,
                            'type' => $barang->isStokHabis() ? 'stok_habis' : 'stok_kritis',
                            'is_read' => false,
                        ], [
                            'message' => $barang->isStokHabis()
                                ? "Stok {$barang->nama_barang} habis!"
                                : "Stok {$barang->nama_barang} mencapai batas minimum ({$barang->stok_sekarang} {$barang->base_unit})!",
                        ]);
                    }
                } else {
                    $namaBarangManual = $item['nama_manual'] ?? 'Barang Manual';
                    $totalHarga = $item['jumlah'] * $item['harga_jual'];

                    BarangKeluarItem::create([
                        'barang_keluar_id' => $barangKeluar->id,
                        'barang_id' => null,
                        'nama_barang_manual' => $namaBarangManual,
                        'jumlah' => $item['jumlah'],
                        'unit_name' => $item['unit_name'],
                        'jumlah_in_base_unit' => $item['jumlah'],
                        'harga_jual' => $item['harga_jual'],
                        'total_harga' => $totalHarga,
                    ]);
                }
            }

            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'model_type' => 'BarangKeluar',
                'model_id' => $barangKeluar->id,
                'description' => "Memperbarui barang keluar: {$barangKeluar->no_transaksi}",
            ]);

            DB::commit();

            return redirect()->route('barang-keluar.show', $barangKeluar)
                ->with('success', 'Transaksi barang keluar berhasil diperbarui')
                ->with('no_transaksi', $barangKeluar->no_transaksi);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating Barang Keluar:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function print(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load(['customer', 'user', 'items' => function ($query) {
            $query->with(['barang.itemUnits']);
        }]);

        // Return view khusus untuk cetak
        return view('barang_keluar.print', compact('barangKeluar'));
    }

    public function destroy(BarangKeluar $barangKeluar)
    {
        DB::beginTransaction();

        try {
            $barangKeluar->load('items.barang');

            // Kembalikan stok untuk setiap item
            foreach ($barangKeluar->items as $item) {
                $barang = $item->barang;
                $barang->stok_sekarang += $item->jumlah_in_base_unit;
                $barang->save();

                // Delete stock history
                StockHistory::where('referensi_tabel', 'barang_keluar_items')
                    ->where('referensi_id', $item->id)
                    ->delete();
            }

            // Delete items
            $barangKeluar->items()->delete();

            // Log activity
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'model_type' => 'BarangKeluar',
                'model_id' => $barangKeluar->id,
                'description' => "Menghapus transaksi barang keluar: {$barangKeluar->no_transaksi}",
            ]);

            // Delete main transaction
            $barangKeluar->delete();

            DB::commit();

            return redirect()->route('barang-keluar.index')
                ->with('success', 'Transaksi barang keluar berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Autocomplete methods
    public function autocompleteBarang(Request $request)
    {
        $search = $request->q;

        $barangs = Barang::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")  // Perbaiki: ganti 'nama' ke 'nama_barang'
                    ->orWhere('kode_barang', 'like', "%{$search}%"); // Perbaiki: ganti 'kode' ke 'kode_barang'
            })
            ->with('itemUnits')
            ->where('stok_sekarang', '>', 0) // Tambahkan filter stok > 0
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $barangs->map(function ($barang) {
                // Tentukan status stok
                $stok = $barang->stok_sekarang;
                $stokStatus = 'bg-success';
                if ($stok <= 0) {
                    $stokStatus = 'bg-danger';
                } elseif ($stok <= $barang->stok_minimum) {
                    $stokStatus = 'bg-warning';
                }

                return [
                    'id'            => $barang->id,
                    'text'          => $barang->nama_barang,
                    'nama'          => $barang->nama_barang,
                    'kode'          => $barang->kode_barang,
                    'stok'          => $stok,
                    'satuan'        => $barang->base_unit, // Gunakan base_unit
                    'stok_status'   => $stokStatus,
                    'stok_minimum'  => $barang->stok_minimum,
                    'units'         => $barang->itemUnits ?? [],
                    'harga_jual'    => $barang->harga_jual,
                ];
            })
        ]);
    }


    public function autocompleteCustomer(Request $request)
    {
        $search = $request->q;

        $customers = Customer::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama_customer', 'like', "%{$search}%")  // Perbaiki: ganti 'nama' ke 'nama_customer'
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('kode_customer', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $customers->map(function ($customer) {
                return [
                    'id'     => $customer->id,
                    'text'   => $customer->nama_customer .
                        ($customer->no_hp ? ' (' . $customer->no_hp . ')' : '') .
                        ($customer->kode_customer ? ' [' . $customer->kode_customer . ']' : ''),
                    'nama'   => $customer->nama_customer,
                    'no_hp'  => $customer->no_hp,
                    'kode'   => $customer->kode_customer,
                ];
            })
        ]);
    }
}
