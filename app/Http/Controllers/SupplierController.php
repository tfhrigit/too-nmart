<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Activity;
use App\Http\Requests\SupplierRequest;
use Illuminate\Http\Request;
use App\Models\BarangMasukTransaksi;


class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_supplier', 'like', "%{$search}%")
                    ->orWhere('nama_supplier', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(20);

        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $data['kode_supplier'] = Supplier::generateKode();

        $supplier = Supplier::create($data);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'Supplier',
            'model_id' => $supplier->id,
            'description' => "Menambahkan supplier: {$supplier->nama_supplier}",
        ]);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Supplier berhasil ditambahkan'
            ]);
        }

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'Supplier',
            'model_id' => $supplier->id,
            'description' => "Mengupdate supplier: {$supplier->nama_supplier}",
        ]);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy(Supplier $supplier)
    {
        $namaSupplier = $supplier->nama_supplier;

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'Supplier',
            'model_id' => $supplier->id,
            'description' => "Menghapus supplier: {$namaSupplier}",
        ]);

        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus');
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|min:3|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $supplier = Supplier::create([
            'kode_supplier' => Supplier::generateKode(),
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat ?? 'Belum diisi',
            'no_hp' => $request->no_hp ?? 'Belum diisi',
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'supplier' => [
                'id' => $supplier->id,
                'nama_supplier' => $supplier->nama_supplier,
            ],
            'message' => 'Supplier berhasil ditambahkan'
        ]);
    }

    // SupplierController.php - method show
    public function show(Supplier $supplier, Request $request)
    {
        // Query barang masuk dengan filter search
        $query = BarangMasukTransaksi::where('supplier_id', $supplier->id)
            ->with(['items.barang', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhere('invoice_supplier', 'like', "%{$search}%")
                    ->orWhereHas('items.barang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('kode_barang', 'like', "%{$search}%");
                    });
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_akhir
            ]);
        }

        $transactions = $query->paginate(10);

        // Hitung statistik
        $totalTransactions = BarangMasukTransaksi::where('supplier_id', $supplier->id)->count();

        // Total nilai dari grand_total transaksi
        $totalValue = BarangMasukTransaksi::where('supplier_id', $supplier->id)
            ->sum('grand_total');

        return view('supplier.show', compact(
            'supplier',
            'transactions',
            'totalTransactions',
            'totalValue'
        ));
    }
}
