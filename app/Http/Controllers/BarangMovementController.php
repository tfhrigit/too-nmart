<?php

namespace App\Http\Controllers;

use App\Models\BarangMovement;
use App\Models\StockHistory;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BarangMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:owner,staff_gudang')->only(['index', 'show', 'riwayatStok']);
    }

    public function index(Request $request): View
    {
        $query = BarangMovement::with('barang');

        // Search by barang name atau kode
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter by tahun
        if ($request->has('tahun') && $request->tahun !== null) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by bulan
        if ($request->has('bulan') && $request->bulan !== null) {
            $query->where('bulan', $request->bulan);
        }

        // Filter by barang
        if ($request->has('barang_id') && $request->barang_id !== null) {
            $query->where('barang_id', $request->barang_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $movements = $query->paginate(15);
        $barangs = Barang::orderBy('nama_barang')->get();

        return view('barang-movement.index', compact('movements', 'barangs'));
    }

    public function show($id): View
    {
        $movement = BarangMovement::with('barang')->findOrFail($id);
        return view('barang-movement.show', compact('movement'));
    }

    /**
     * Riwayat Stok - Stock movement history with daily tracking
     */
    public function riwayatStok(Request $request): View
    {
        // Periode filter (default 3 bulan)
        $periodeDefault = $request->get('periode', '3');
        $endDate = Carbon::now()->endOfDay();
        $startDate = $endDate->copy()->subMonths((int)$periodeDefault)->startOfDay();

        // Search filter
        $search = $request->get('search', '');
        
        // Get all barangs with stock history in the date range
        $barangsQuery = Barang::query();
        
        if ($search) {
            $barangsQuery->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
        }
        
        $barangs = $barangsQuery->orderBy('nama_barang')->get();

        // Get date range for column headers
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);
        $dates = collect($dateRange)->toArray();

        // Build stock history data
        $stockData = [];
        foreach ($barangs as $barang) {
            $history = StockHistory::where('barang_id', $barang->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal')
                ->get();

            $dailyData = [];
            $stokAwal = (int)$barang->stok_sekarang; // Ambil dari inputan barang

            // Calculate total masuk/keluar dalam periode
            $totalMasuk = $history->where('jenis_transaksi', 'masuk')->sum('jumlah');
            $totalKeluar = $history->where('jenis_transaksi', 'keluar')->sum('jumlah');
            
            // Calculate current stock
            $stokSaatIni = $stokAwal + $totalMasuk - $totalKeluar;

            foreach ($dates as $date) {
                $dayHistories = $history->filter(function($h) use ($date) {
                    return $h->tanggal->format('Y-m-d') === $date->format('Y-m-d');
                });

                $masuk = $dayHistories->where('jenis_transaksi', 'masuk')->sum('jumlah');
                $keluar = $dayHistories->where('jenis_transaksi', 'keluar')->sum('jumlah');

                $dailyData[$date->format('Y-m-d')] = [
                    'masuk' => $masuk,
                    'keluar' => $keluar,
                ];
            }

            if (count($dailyData) > 0 || $history->count() == 0) {
                $stockData[] = [
                    'barang' => $barang,
                    'daily' => $dailyData,
                    'stok_awal' => $stokAwal,
                    'stok_saat_ini' => $stokSaatIni,
                    'jumlah_masuk' => $totalMasuk,
                    'jumlah_keluar' => $totalKeluar,
                ];
            }
        }

        $barangsList = Barang::orderBy('nama_barang')->get();

        return view('barang-movement.riwayat-stok', compact(
            'stockData',
            'dates',
            'startDate',
            'endDate',
            'periodeDefault',
            'search',
            'barangsList'
        ));
    }
}
