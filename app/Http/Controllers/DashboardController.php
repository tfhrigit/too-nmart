<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasukTransaksi;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Activity;
use App\Models\Notification;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Total stok barang
        $totalBarang = Barang::count();
        
        // Barang dengan stok kritis
        $stokKritis = Barang::whereRaw('stok_sekarang <= stok_minimum')->count();
        
        // Barang dengan stok 0
        $stokHabis = Barang::where('stok_sekarang', 0)->count();
        
        // Transaksi bulan ini
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanIniEnd = Carbon::now()->endOfMonth();
        
        // PERBAIKAN 1: Total pengeluaran (pembelian barang masuk)
        $totalMasuk = BarangMasukTransaksi::whereBetween('tanggal', [$bulanIni, $bulanIniEnd])
            ->sum('grand_total');
        
        // PERBAIKAN 2: Total pendapatan (penjualan barang keluar)
        $totalKeluar = BarangKeluarItem::whereHas('barangKeluar', function($query) use ($bulanIni, $bulanIniEnd) {
            $query->whereBetween('tanggal', [$bulanIni, $bulanIniEnd]);
        })->sum('total_harga');
        
        // Total Customer dan Supplier
        $totalCustomer = Customer::count();
        $totalSupplier = Supplier::count();
        
        // Data untuk grafik pendapatan & pengeluaran 6 bulan terakhir
        $chartData = $this->getRevenueExpenseChartData();
        
        // Data untuk grafik pie (total barang vs stok habis)
        $pieChartData = $this->getInventoryPieChartData($totalBarang, $stokHabis);
        
        // Barang stok rendah (untuk tabel)
        $barangStokRendah = Barang::whereRaw('stok_sekarang <= stok_minimum')
            ->orderBy('stok_sekarang', 'asc')
            ->limit(10)
            ->get();
        
        // Recent activities
        $recentActivities = Activity::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        // Unread notifications
        $notifications = Notification::with('barang')
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();
        
        // Top 5 barang terlaris bulan ini
        $topBarang = BarangKeluarItem::select('barang_id', 
                DB::raw('SUM(jumlah) as total_terjual'), 
                DB::raw('SUM(total_harga) as total_penjualan'))
            ->whereHas('barangKeluar', function($query) use ($bulanIni, $bulanIniEnd) {
                $query->whereBetween('tanggal', [$bulanIni, $bulanIniEnd]);
            })
            ->whereNotNull('barang_id')
            ->groupBy('barang_id')
            ->orderBy('total_terjual', 'desc')
            ->with('barang')
            ->limit(5)
            ->get();
        
        // Top 5 barang tidak laku 3 bulan terakhir
        $topBarangTidakLaku = $this->getTopBarangTidakLaku();
        
        // PERBAIKAN 3: Tambahkan log untuk debugging
        \Log::info('Dashboard Data:', [
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'bulanIni' => $bulanIni->format('Y-m-d'),
            'bulanIniEnd' => $bulanIniEnd->format('Y-m-d')
        ]);
        
        return view('dashboard.index', compact(
            'totalBarang',
            'stokKritis',
            'stokHabis',
            'totalMasuk',
            'totalKeluar',
            'totalCustomer',
            'totalSupplier',
            'chartData',
            'pieChartData',
            'barangStokRendah',
            'recentActivities',
            'notifications',
            'topBarang',
            'topBarangTidakLaku'
        ));
    }
    
    private function getRevenueExpenseChartData()
    {
        $months = [];
        $revenues = []; // Pendapatan (Penjualan)
        $expenses = []; // Pengeluaran (Pembelian)
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            // PERBAIKAN 4: Pengeluaran dari BarangMasukTransaksi
            $pengeluaran = BarangMasukTransaksi::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('grand_total');
            
            // PERBAIKAN 5: Pendapatan dari BarangKeluarItem
            $pendapatan = BarangKeluarItem::whereHas('barangKeluar', function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
            })->sum('total_harga');
            
            $months[] = $date->format('M Y');
            $revenues[] = $pendapatan;
            $expenses[] = $pengeluaran;
        }
        
        return [
            'labels' => $months,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }
    
    private function getInventoryPieChartData($totalBarang, $stokHabis)
    {
        $barangTersedia = $totalBarang - $stokHabis;
        
        return [
            'labels' => ['Barang Tersedia', 'Barang Habis'],
            'data' => [$barangTersedia, $stokHabis],
            'colors' => ['#1e40af', '#60a5fa'],
        ];
    }
    
    private function getTopBarangTidakLaku()
    {
        $tigaBulanLalu = Carbon::now()->subMonths(3)->startOfMonth();
        
        // Ambil semua barang yang memiliki penjualan dalam 3 bulan terakhir
        $barangIds = BarangKeluarItem::whereHas('barangKeluar', function($query) use ($tigaBulanLalu) {
            $query->where('tanggal', '>=', $tigaBulanLalu);
        })
        ->whereNotNull('barang_id')
        ->groupBy('barang_id')
        ->pluck('barang_id')
        ->toArray();
        
        // Barang yang TIDAK ada dalam daftar barang terjual 3 bulan terakhir
        $barangTidakLaku = Barang::whereNotIn('id', $barangIds)
            ->where('created_at', '<=', $tigaBulanLalu)
            ->select('id', 'nama_barang', 'kode_barang', 'stok_sekarang', 'base_unit', 'created_at')
            ->orderBy('stok_sekarang', 'desc')
            ->limit(5)
            ->get();
        
        return $barangTidakLaku;
    }
    
    /**
     * Clear activity logs
     */
    public function clearActivities(Request $request)
    {
        Activity::truncate();
        
        return redirect()->back()->with('success', 'Log aktivitas berhasil dibersihkan');
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $notification = Notification::find($id);
        
        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
}