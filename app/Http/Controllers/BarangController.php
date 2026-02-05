<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ItemUnit;
use App\Models\Activity;
use App\Http\Requests\BarangRequest;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function __construct()
    {
        // Terapkan middleware untuk proteksi berdasarkan role
        $this->middleware('role:owner,staff_gudang')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'storeAjax'
        ]);

        // Index dan show boleh diakses oleh semua role
        $this->middleware('auth')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Barang::with('itemUnits');

        // Filter stok kritis (kurang dari atau sama dengan stok minimum, tapi stok > 0)
        if ($request->has('filter') && $request->filter === 'kritis') {
            $query->where('stok_sekarang', '>', 0)
                ->whereRaw('stok_sekarang <= stok_minimum');
        }

        // Filter barang kosong (stok = 0)
        if ($request->has('filter') && $request->filter === 'kosong') {
            $query->where('stok_sekarang', '<=', 0);
        }

        // Jika kasir, hanya tampilkan barang dengan stok > 0
        if ($user->isKasir()) {
            $query->where('stok_sekarang', '>', 0);
        }

        $barangs = $query->latest()->paginate(10);

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.create')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah barang');
        }

        // Ambil semua satuan yang pernah digunakan untuk autocomplete
        $existingUnits = Barang::select('base_unit')
            ->distinct()
            ->get()
            ->pluck('base_unit')
            ->toArray();

        // Ambil juga dari item_units
        $existingItemUnits = ItemUnit::select('unit_name')
            ->distinct()
            ->get()
            ->pluck('unit_name')
            ->toArray();

        $allUnits = array_unique(array_merge($existingUnits, $existingItemUnits));

        return view('barang.create', compact('allUnits'));
    }
    public function store(BarangRequest $request)
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.create')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah barang');
        }

        // Validasi tambahan untuk satuan
        $request->validate([
            'base_unit' => 'required|string|min:1|max:20',
            'stok_minimum' => 'required|numeric|min:0',
            'stok_awal' => 'nullable|numeric|min:0',
            'units.*.name' => 'nullable|string|min:1|max:20',
            'units.*.multiplier' => 'nullable|numeric|min:0.01',
        ]);

        $data = $request->validated();
        $data['kode_barang'] = Barang::generateKode();
        $data['stok_sekarang'] = $request->stok_awal ?? 0;

        // Validasi harga
        if ($request->harga_beli && $request->harga_beli < 0) {
            return back()->withErrors(['harga_beli' => 'Harga beli tidak boleh negatif'])->withInput();
        }
        if ($request->harga_jual && $request->harga_jual < 0) {
            return back()->withErrors(['harga_jual' => 'Harga jual tidak boleh negatif'])->withInput();
        }

        // Validasi satuan tambahan duplikat
        if ($request->has('units')) {
            $unitNames = [];
            foreach ($request->units as $unit) {
                if (!empty($unit['name'])) {
                    if (in_array(strtolower($unit['name']), array_map('strtolower', $unitNames))) {
                        return back()->withErrors(['units' => 'Satuan tambahan tidak boleh duplikat'])->withInput();
                    }
                    $unitNames[] = $unit['name'];
                }
            }
        }

        // Harga beli & jual sudah divalidasi di BarangRequest
        $barang = Barang::create($data);

        // Create base unit
        ItemUnit::create([
            'barang_id' => $barang->id,
            'unit_name' => $data['base_unit'],
            'multiplier' => 1,
            'is_base' => true,
        ]);

        // Create additional units jika ada
        if ($request->has('units')) {
            foreach ($request->units as $unit) {
                if (!empty($unit['name']) && !empty($unit['multiplier'])) {
                    // Validasi satuan tidak sama dengan base unit
                    if (strtolower($unit['name']) === strtolower($data['base_unit'])) {
                        return back()->withErrors(['units' => 'Satuan tambahan tidak boleh sama dengan satuan dasar'])->withInput();
                    }

                    ItemUnit::create([
                        'barang_id' => $barang->id,
                        'unit_name' => $unit['name'],
                        'multiplier' => $unit['multiplier'],
                        'is_base' => false,
                    ]);
                }
            }
        }

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'Barang',
            'model_id' => $barang->id,
            'description' => "Menambahkan barang: {$barang->nama_barang}",
        ]);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            // Load relationships untuk response
            $barang->load('itemUnits');

            return response()->json([
                'success' => true,
                'barang' => $barang,
                'message' => 'Barang berhasil ditambahkan'
            ]);
        }

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function show(Barang $barang)
    {
        // Cek izin melihat barang
        if (!auth()->user()->hasPermission('barang.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail barang');
        }

        $barang->load([
            'itemUnits',
            'barangMasuks.supplier',
            'barangKeluarItems.barangKeluar.customer',
            'stockHistories'
        ]);

        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit barang');
        }

        // Ambil semua satuan yang pernah digunakan untuk autocomplete
        $existingUnits = Barang::select('base_unit')
            ->distinct()
            ->get()
            ->pluck('base_unit')
            ->toArray();

        $existingItemUnits = ItemUnit::select('unit_name')
            ->distinct()
            ->get()
            ->pluck('unit_name')
            ->toArray();

        $allUnits = array_unique(array_merge($existingUnits, $existingItemUnits));

        $barang->load('itemUnits');
        return view('barang.edit', compact('barang', 'allUnits'));
    }
    public function update(BarangRequest $request, Barang $barang)
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit barang');
        }

        // Validasi tambahan
        $request->validate([
            'base_unit' => 'required|string|min:1|max:20',
            'stok_minimum' => 'required|numeric|min:0',
            'units.*.name' => 'nullable|string|min:1|max:20',
            'units.*.multiplier' => 'nullable|numeric|min:0.01',
        ]);

        // Validasi harga
        if ($request->harga_beli && $request->harga_beli < 0) {
            return back()->withErrors(['harga_beli' => 'Harga beli tidak boleh negatif'])->withInput();
        }
        if ($request->harga_jual && $request->harga_jual < 0) {
            return back()->withErrors(['harga_jual' => 'Harga jual tidak boleh negatif'])->withInput();
        }

        // Validasi satuan tambahan duplikat
        if ($request->has('units')) {
            $unitNames = [];
            foreach ($request->units as $unit) {
                if (!empty($unit['name'])) {
                    // Cek duplikat dengan satuan lain
                    if (in_array(strtolower($unit['name']), array_map('strtolower', $unitNames))) {
                        return back()->withErrors(['units' => 'Satuan tambahan tidak boleh duplikat'])->withInput();
                    }

                    // Cek tidak sama dengan satuan dasar
                    if (strtolower($unit['name']) === strtolower($request->base_unit)) {
                        return back()->withErrors(['units' => 'Satuan tambahan tidak boleh sama dengan satuan dasar'])->withInput();
                    }

                    $unitNames[] = $unit['name'];
                }
            }
        }

        $data = $request->validated();
        $barang->update($data);

        // Update base unit
        $barang->itemUnits()->where('is_base', true)->update([
            'unit_name' => $data['base_unit']
        ]);

        // Update units
        if ($request->has('units')) {
            // Keep base unit, remove others and recreate
            $barang->itemUnits()->where('is_base', false)->delete();

            foreach ($request->units as $unit) {
                if (!empty($unit['name']) && !empty($unit['multiplier'])) {
                    ItemUnit::create([
                        'barang_id' => $barang->id,
                        'unit_name' => $unit['name'],
                        'multiplier' => $unit['multiplier'],
                        'is_base' => false,
                    ]);
                }
            }
        }

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'Barang',
            'model_id' => $barang->id,
            'description' => "Mengupdate barang: {$barang->nama_barang}",
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus barang');
        }

        // Cek apakah barang memiliki transaksi
        if ($barang->barangMasuks()->exists() || $barang->barangKeluars()->exists()) {
            return redirect()->route('barang.index')
                ->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat transaksi');
        }

        $namaBarang = $barang->nama_barang;

        // Log activity before delete
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'Barang',
            'model_id' => $barang->id,
            'description' => "Menghapus barang: {$namaBarang}",
        ]);

        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus');
    }

    public function storeAjax(Request $request)
    {
        // Hanya owner dan staff gudang yang bisa akses
        if (!auth()->user()->hasPermission('barang.create')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menambah barang'
            ], 403);
        }

        $request->validate([
            'nama_barang' => 'required|string|min:3|max:255',
            'base_unit' => 'required|string',
            'stok_minimum' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        $barang = Barang::create([
            'kode_barang' => Barang::generateKode(),
            'nama_barang' => $request->nama_barang,
            'base_unit' => $request->base_unit,
            'stok_sekarang' => 0,
            'stok_minimum' => $request->stok_minimum,
            'deskripsi' => $request->deskripsi,
            'harga_beli' => $request->input('harga_beli', 0),
            'harga_jual' => $request->input('harga_jual', 0),
        ]);

        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'Barang',
            'model_id' => $barang->id,
            'description' => "Menambahkan barang via AJAX: {$barang->nama_barang}",
        ]);

        // Load item_units relationship untuk response
        $barang->load('itemUnits');

        return response()->json([
            'success' => true,
            'barang' => $barang,
            'message' => 'Barang berhasil ditambahkan'
        ]);
    }

    public function getUnits(Barang $barang)
    {
        // Semua role boleh mengakses karena diperlukan untuk transaksi
        $units = $barang->itemUnits()->get(['id', 'unit_name', 'multiplier']);
        return response()->json($units);
    }

    // Method untuk API data barang (dipakai di barang keluar)
    public function apiBarang(Request $request)
    {
        $query = Barang::with('itemUnits');

        // Jika kasir, hanya tampilkan barang dengan stok > 0
        if (auth()->user()->isKasir()) {
            $query->where('stok_sekarang', '>', 0);
        }

        // Search
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        $barangs = $query->get()->map(function ($barang) {
            return [
                'id' => $barang->id,
                'text' => "{$barang->nama_barang} ({$barang->kode_barang})",
                'kode' => $barang->kode_barang,
                'nama' => $barang->nama_barang,
                'stok' => $barang->stok_sekarang,
                'base_unit' => $barang->base_unit,
                'item_units' => $barang->itemUnits,
                'harga_beli' => $barang->harga_beli,
                'harga_jual' => $barang->harga_jual,
            ];
        });

        return response()->json($barangs);
    }
}
