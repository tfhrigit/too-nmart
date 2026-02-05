<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Activity;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;



class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_customer', 'like', "%{$search}%")
                    ->orWhere('nama_customer', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20);

        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();
        $data['kode_customer'] = Customer::generateKode();

        $customer = Customer::create($data);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'Customer',
            'model_id' => $customer->id,
            'description' => "Menambahkan pelanggan: {$customer->nama_customer}",
        ]);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => 'Pelanggan berhasil ditambahkan'
            ]);
        }

        return redirect()->route('customer.index')
            ->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'Customer',
            'model_id' => $customer->id,
            'description' => "Mengupdate pelanggan: {$customer->nama_customer}",
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Pelanggan berhasil diupdate');
    }

    // CustomerController.php
    public function storeAjax(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|min:3|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $customer = Customer::create([
            'kode_customer' => Customer::generateKode(),
            'nama_customer' => $request->nama_customer,
            'alamat' => $request->alamat ?? 'Belum diisi',
            'no_hp' => $request->no_hp ?? 'Belum diisi',
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'nama_customer' => $customer->nama_customer,
            ],
            'message' => 'Customer berhasil ditambahkan'
        ]);
    }

    public function destroy(Customer $customer)
    {
        $namaCustomer = $customer->nama_customer;

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'Customer',
            'model_id' => $customer->id,
            'description' => "Menghapus pelanggan: {$namaCustomer}",
        ]);

        $customer->delete();

        return redirect()->route('customer.index')
            ->with('success', 'Pelanggan berhasil dihapus');
    }

    public function show(Customer $customer)
    {
        // Ambil transaksi barang keluar dari customer ini (FIXED: melalui barang_keluar)
        $barangKeluars = BarangKeluar::where('customer_id', $customer->id)
            ->with(['items.barang', 'user'])
            ->latest('tanggal')
            ->paginate(10);

        // Hitung statistik dari barang_keluar
        $totalTransactions = BarangKeluar::where('customer_id', $customer->id)->count();

        // Hitung total nilai dari semua items dalam semua transaksi
        $totalValue = 0;
        $barangKeluars->each(function ($transaction) use (&$totalValue) {
            $totalValue += $transaction->items->sum('total_harga');
        });

        $averageTransaction = $totalTransactions > 0 ? $totalValue / $totalTransactions : 0;

        // Ambil transaksi terakhir
        $lastTransaction = BarangKeluar::where('customer_id', $customer->id)
            ->latest('tanggal')
            ->first();
        $lastTransactionDate = $lastTransaction ? $lastTransaction->tanggal->format('d/m/Y') : '-';

        // Ambil barang favorit (yang paling sering dibeli)
        $favoriteProducts = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
            ->whereNotNull('barang_id')
            ->selectRaw('barang_id, 
                    SUM(jumlah_in_base_unit) as total_qty,
                    SUM(total_harga) as total_value,
                    COUNT(*) as transaction_count')
            ->groupBy('barang_id')
            ->orderByDesc('total_value')
            ->limit(4)
            ->with('barang')
            ->get();

        return view('customer.show', compact(
            'customer',
            'barangKeluars', // Ubah dari 'transactions' ke 'barangKeluars'
            'totalTransactions',
            'totalValue',
            'averageTransaction',
            'lastTransactionDate',
            'favoriteProducts'
        ));
    }
}
