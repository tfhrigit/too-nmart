<?php

namespace App\Http\Controllers;

use App\Models\BarangMasukTransaksi;
use App\Models\BarangMasukItem;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Barang;
use App\Models\MonthlyReport;
use App\Models\BarangMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Laporan Transaksi Harian/Mingguan/Bulanan
    public function index(Request $request): View
    {
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));

        // Dapatkan summary data
        $summary = $this->getSummaryData($tanggalMulai, $tanggalAkhir);

        // Ambil data barang masuk
        $barangMasukTransaksis = BarangMasukTransaksi::with(['supplier', 'items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest('tanggal')
            ->get();

        // Ambil data barang keluar
        $barangKeluarTransaksis = BarangKeluar::with(['customer', 'items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest('tanggal')
            ->get();

        // **DIUBAH**: Hitung top barang per transaksi (tidak dikelompokkan)
        $topBarang = $this->getTopBarangPerTransaction($tanggalMulai, $tanggalAkhir);

        // Hitung profit per transaksi
        $profitPerTransaksi = $this->getProfitPerTransaksi($tanggalMulai, $tanggalAkhir);

        // Data pembayaran
        $summaryPembayaran = $this->getSummaryPembayaran($tanggalMulai, $tanggalAkhir);

        return view('laporan.index', compact(
            'tanggalMulai',
            'tanggalAkhir',
            'summary',
            'barangMasukTransaksis',
            'barangKeluarTransaksis',
            'topBarang',
            'profitPerTransaksi',
            'summaryPembayaran'
        ));
    }

    // **DIUBAH**: Method baru untuk mengambil top barang per transaksi
    private function getTopBarangPerTransaction($tanggalMulai, $tanggalAkhir)
    {
        // Ambil semua item barang keluar dalam periode
        $items = BarangKeluarItem::with(['barang', 'barangKeluar'])
            ->whereHas('barangKeluar', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->whereNotNull('barang_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $result = [];

        foreach ($items as $item) {
            if (!$item->barang) continue;

            // Hitung profit untuk item ini
            $hargaBeli = $this->getHargaBeliBarang(
                $item->barang_id,
                $tanggalMulai,
                $tanggalAkhir,
                $item->jumlah_in_base_unit
            );

            $totalPembelian = $hargaBeli * $item->jumlah_in_base_unit;
            $profit = $item->total_harga - $totalPembelian;

            $result[] = [
                'id' => $item->id,
                'barang' => $item->barang,
                'barang_id' => $item->barang_id,
                'nama_barang' => $item->barang->nama_barang,
                'kode_barang' => $item->barang->kode_barang,
                'no_transaksi' => $item->barangKeluar->no_transaksi,
                'tanggal_transaksi' => $item->barangKeluar->tanggal,
                'jumlah_terjual' => $item->jumlah,
                'jumlah_in_base_unit' => $item->jumlah_in_base_unit,
                'unit_name' => $item->unit_name,
                'harga_jual' => $item->harga_jual,
                'total_penjualan' => $item->total_harga,
                'harga_beli' => $hargaBeli,
                'total_pembelian' => $totalPembelian,
                'profit' => $profit,
                'profit_percentage' => $totalPembelian > 0 ? ($profit / $totalPembelian) * 100 : 0,
                'customer' => $item->barangKeluar->customer ? $item->barangKeluar->customer->nama_customer : 'Umum',
                'metode_pembayaran' => $item->barangKeluar->metode_pembayaran
            ];
        }

        // Urutkan berdasarkan total penjualan tertinggi
        usort($result, function ($a, $b) {
            return $b['total_penjualan'] <=> $a['total_penjualan'];
        });

        // Ambil top 10
        return array_slice($result, 0, 10);
    }
    // Method baru untuk mengambil top barang dari barang_keluar_items
    // Method untuk mengambil top barang
    // Method lama tetap ada untuk kompatibilitas
    private function getTopBarang($tanggalMulai, $tanggalAkhir)
    {
        return BarangKeluarItem::select(
            'barang_id',
            DB::raw('SUM(jumlah) as total_terjual'),
            DB::raw('SUM(total_harga) as total_penjualan')
        )
            ->whereHas('barangKeluar', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->whereNotNull('barang_id')
            ->groupBy('barang_id')
            ->orderBy('total_terjual', 'desc')
            ->with('barang')
            ->limit(10)
            ->get();
    }

    // Method untuk mendapatkan harga beli barang
    private function getHargaBeliBarang($barangId, $tanggalMulai, $tanggalAkhir, $jumlahKeluar)
    {
        // Ambil harga beli terakhir dari barang masuk dalam periode
        $hargaBeliTerakhir = BarangMasukItem::where('barang_id', $barangId)
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->latest('created_at')
            ->value('harga_beli');

        if ($hargaBeliTerakhir) {
            return $hargaBeliTerakhir;
        }

        // Ambil rata-rata harga beli dalam periode
        $avgHargaBeli = BarangMasukItem::where('barang_id', $barangId)
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->avg('harga_beli');

        if ($avgHargaBeli) {
            return $avgHargaBeli;
        }

        // Ambil harga beli dari master barang
        $barang = Barang::find($barangId);
        return $barang->harga_beli ?? 0;
    }

    // PERBAIKAN: Method untuk mendapatkan harga beli barang (tanpa sisa_stok)
    private function getHargaBeliBarangV2($barangId, $tanggalMulai, $tanggalAkhir, $jumlahKeluar)
    {
        // Cara 1: Ambil harga beli terakhir dari barang masuk dalam periode
        $hargaBeliTerakhir = BarangMasukItem::where('barang_id', $barangId)
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->latest('created_at')
            ->value('harga_beli');

        if ($hargaBeliTerakhir) {
            return $hargaBeliTerakhir;
        }

        // Cara 2: Ambil rata-rata harga beli dalam periode
        $avgHargaBeli = BarangMasukItem::where('barang_id', $barangId)
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->avg('harga_beli');

        if ($avgHargaBeli) {
            return $avgHargaBeli;
        }

        // Cara 3: Ambil harga beli dari master barang
        $barang = Barang::find($barangId);
        return $barang->harga_beli ?? 0;
    }

    // Method untuk menghitung profit per transaksi
    private function getProfitPerTransaksi($tanggalMulai, $tanggalAkhir)
    {
        $profitData = [];

        // Ambil semua transaksi barang keluar dalam periode
        $transaksis = BarangKeluar::with(['items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest('tanggal')
            ->get();

        foreach ($transaksis as $transaksi) {
            $totalPenjualan = $transaksi->items->sum('total_harga');
            $totalPembelian = 0;
            $itemCount = 0;
            $itemsDetail = [];

            // Hitung per item
            foreach ($transaksi->items as $item) {
                if ($item->barang_id) {
                    $hargaBeli = $this->getHargaBeliBarang(
                        $item->barang_id,
                        $tanggalMulai,
                        $tanggalAkhir,
                        $item->jumlah_in_base_unit
                    );

                    $pembelianItem = $hargaBeli * $item->jumlah_in_base_unit;
                    $totalPembelian += $pembelianItem;
                } else {
                    // Untuk barang manual, anggap pembelian = 80% dari harga jual
                    $pembelianManual = $item->total_harga * 0.8;
                    $totalPembelian += $pembelianManual;
                }
                $itemCount++;
            }

            // Hitung profit
            $profit = $totalPenjualan - $totalPembelian;

            // Simpan data
            $profitData[] = [
                'no_transaksi' => $transaksi->no_transaksi,
                'tanggal' => $transaksi->tanggal,
                'customer' => $transaksi->customer ? $transaksi->customer->nama_customer : 'Umum',
                'item_count' => $itemCount,
                'total_penjualan' => $totalPenjualan,
                'total_pembelian' => $totalPembelian,
                'profit' => $profit,
                'metode_pembayaran' => $transaksi->metode_pembayaran
            ];
        }

        // Urutkan berdasarkan tanggal terbaru
        usort($profitData, function ($a, $b) {
            return $b['tanggal'] <=> $a['tanggal'];
        });

        return $profitData;
    }

    // Helper untuk mendapatkan rata-rata harga beli dalam periode
    private function getAverageHargaBeli($barangId, $tanggalMulai, $tanggalAkhir)
    {
        return BarangMasukItem::where('barang_id', $barangId)
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
            })
            ->avg('harga_beli') ?? 0;
    }

    // Method generateReport untuk menyesuaikan dengan model baru
    public static function generateReport($tahun, $bulan)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Total pembelian dari barang masuk transaksi
        $totalPembelian = BarangMasukTransaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('grand_total');

        // Total penjualan dari barang keluar items - PERBAIKAN DI SINI
        $totalPenjualan = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        })->sum('total_harga');

        // Breakdown per metode pembayaran - PERBAIKAN DI SINI
        // Untuk cash
        $totalCash = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate])
                ->where('metode_pembayaran', 'cash');
        })->sum('total_harga');

        // Untuk qris
        $totalQris = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate])
                ->where('metode_pembayaran', 'qris');
        })->sum('total_harga');

        // Untuk transfer
        $totalTransfer = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate])
                ->where('metode_pembayaran', 'transfer');
        })->sum('total_harga');

        // Jumlah transaksi
        $jumlahTransaksiMasuk = BarangMasukTransaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->count();

        $jumlahTransaksiKeluar = BarangKeluar::whereBetween('tanggal', [$startDate, $endDate])
            ->count();

        // Hitung profit
        $profit = $totalPenjualan - $totalPembelian;
        $profitPercentage = $totalPembelian > 0 ? ($profit / $totalPembelian) * 100 : 0;

        // Update atau create report
        return MonthlyReport::updateOrCreate(
            [
                'tahun' => $tahun,
                'bulan' => $bulan,
            ],
            [
                'bulan_name' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F'),
                'total_pembelian' => $totalPembelian,
                'total_penjualan' => $totalPenjualan,
                'total_penjualan_cash' => $totalCash,
                'total_penjualan_qris' => $totalQris,
                'total_penjualan_transfer' => $totalTransfer,
                'jumlah_transaksi_masuk' => $jumlahTransaksiMasuk,
                'jumlah_transaksi_keluar' => $jumlahTransaksiKeluar,
                'profit' => $profit,
                'profit_percentage' => $profitPercentage,
            ]
        );
    }

    // Laporan Bulanan
    public function laporanBulanan(Request $request): View
    {
        $tahun = $request->input('tahun', Carbon::now()->year);
        $tahunList = range(Carbon::now()->year, Carbon::now()->year - 5);

        // Generate atau update laporan untuk semua bulan di tahun ini
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            self::generateReport($tahun, $bulan);
        }

        $monthlyReports = MonthlyReport::where('tahun', $tahun)
            ->orderBy('bulan', 'asc')
            ->get();

        // Chart data untuk grafik
        $chartData = [
            'labels' => $monthlyReports->pluck('bulan_name')->toArray(),
            'pembelian' => $monthlyReports->pluck('total_pembelian')->toArray(),
            'penjualan' => $monthlyReports->pluck('total_penjualan')->toArray(),
            'profit' => $monthlyReports->pluck('profit')->toArray(),
            'cash' => $monthlyReports->pluck('total_penjualan_cash')->toArray(),
            'qris' => $monthlyReports->pluck('total_penjualan_qris')->toArray(),
            'transfer' => $monthlyReports->pluck('total_penjualan_transfer')->toArray(),
        ];

        // Hitung total untuk footer
        $totals = [
            'pembelian' => $monthlyReports->sum('total_pembelian'),
            'penjualan' => $monthlyReports->sum('total_penjualan'),
            'cash' => $monthlyReports->sum('total_penjualan_cash'),
            'qris' => $monthlyReports->sum('total_penjualan_qris'),
            'transfer' => $monthlyReports->sum('total_penjualan_transfer'),
            'profit' => $monthlyReports->sum('profit'),
        ];

        return view('laporan.bulanan', compact(
            'monthlyReports',
            'tahun',
            'tahunList',
            'chartData',
            'totals'
        ));
    }

    // Laporan Pergerakan Barang
    public function laporanBarang(Request $request): View
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $search = $request->input('search', '');

        // Update movement untuk semua barang
        $barangs = Barang::all();
        foreach ($barangs as $barang) {
            BarangMovement::updateMovement($barang->id, $tahun, $bulan);
        }

        $movementsQuery = BarangMovement::with('barang')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan);

        // Tambahkan filter pencarian jika ada
        if (!empty($search)) {
            $movementsQuery->whereHas('barang', function ($query) use ($search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $search . '%');
            });
        }

        $movements = $movementsQuery
            ->orderBy('total_keluar', 'desc')
            ->get();

        // Statistik
        $stats = [
            'total_barang' => $movements->count(),
            'barang_terjual' => $movements->where('total_keluar', '>', 0)->count(),
            'barang_tidak_terjual' => $movements->where('total_keluar', '=', 0)->count(),
            'total_nilai_masuk' => $movements->sum('nilai_masuk'),
            'total_nilai_keluar' => $movements->sum('nilai_keluar'),
        ];

        return view('laporan.barang', compact('movements', 'bulan', 'tahun', 'search', 'stats'));
    }

    // PERBAIKAN: Method barang tidak laku untuk periode yang difilter
    private function getBarangTidakLaku($tanggalMulai, $tanggalAkhir)
    {
        // Ambil semua barang yang memiliki stok
        $barangs = Barang::where('stok_sekarang', '>', 0)
            ->with(['barangKeluarItems' => function ($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereHas('barangKeluar', function ($q) use ($tanggalMulai, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
                });
            }])
            ->get();

        $barangTidakLaku = [];

        foreach ($barangs as $barang) {
            // Hitung total barang keluar dalam periode
            $totalKeluar = $barang->barangKeluarItems
                ->sum('jumlah_in_base_unit');

            // Jika barang ada stok tapi tidak terjual sama sekali dalam periode yang difilter
            if ($totalKeluar == 0) {
                // Cari tanggal terakhir barang keluar (sebelum periode)
                $lastSale = $barang->barangKeluarItems()
                    ->whereHas('barangKeluar', function ($query) use ($tanggalMulai) {
                        $query->where('tanggal', '<', $tanggalMulai)
                            ->orderBy('tanggal', 'desc');
                    })
                    ->first();

                $hariTidakTerjual = Carbon::now()->diffInDays(
                    $lastSale ? Carbon::parse($lastSale->barangKeluar->tanggal) : Carbon::parse($barang->created_at)
                );

                $barangTidakLaku[] = [
                    'barang' => $barang,
                    'stok_sekarang' => $barang->stok_sekarang,
                    'last_sale_date' => $lastSale ? $lastSale->barangKeluar->tanggal : $barang->created_at,
                    'hari_tidak_terjual' => $hariTidakTerjual,
                    'status' => $hariTidakTerjual > 90 ? 'tidak_laku' : 'perhatian'
                ];
            }
        }

        // Urutkan berdasarkan hari tidak terjual terbanyak
        usort($barangTidakLaku, function ($a, $b) {
            return $b['hari_tidak_terjual'] <=> $a['hari_tidak_terjual'];
        });

        return collect($barangTidakLaku);
    }

    // Laporan Barang Masuk (Detail)
    public function barangMasuk(Request $request): View
    {
        $tanggalMulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-t');

        // Query untuk laporan barang masuk
        $transaksis = BarangMasukTransaksi::with(['supplier', 'items.barang', 'user'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Hitung summary
        $summary = [
            'total_pembelian' => $transaksis->sum('grand_total'),
            'jumlah_transaksi' => $transaksis->count(),
            'total_item' => $transaksis->sum(function ($transaksi) {
                return $transaksi->items->count();
            }),
        ];

        return view('laporan.detail-barang-masuk', compact('transaksis', 'summary', 'tanggalMulai', 'tanggalAkhir'));
    }

    // Laporan Barang Keluar (Detail)
    public function barangKeluar(Request $request): View
    {
        $tanggalMulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-t');

        // Query untuk laporan barang keluar
        $transaksis = BarangKeluar::with(['customer', 'items.barang', 'user'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Hitung total penjualan dari items
        $totalPenjualan = 0;
        foreach ($transaksis as $transaksi) {
            $totalPenjualan += $transaksi->items->sum('total_harga');
        }

        // Hitung summary
        $summary = [
            'total_penjualan' => $totalPenjualan,
            'jumlah_transaksi' => $transaksis->count(),
            'total_item' => $transaksis->sum(function ($transaksi) {
                return $transaksi->items->count();
            }),
        ];

        // Breakdown pembayaran
        $pembayaran = [
            'cash' => 0,
            'qris' => 0,
            'transfer' => 0,
        ];

        foreach ($transaksis as $transaksi) {
            $metode = $transaksi->metode_pembayaran;
            $totalTransaksi = $transaksi->items->sum('total_harga');

            if (isset($pembayaran[$metode])) {
                $pembayaran[$metode] += $totalTransaksi;
            }
        }

        return view('laporan.detail-barang-keluar', compact(
            'transaksis',
            'summary',
            'pembayaran',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    }

    // Laporan Barang Tidak Laku
    public function barangTidakLaku(Request $request): View
    {
        $bulanBatas = $request->input('bulan_batas', 3);
        
        // Gunakan method dari BarangMovement model
        $barangTidakLaku = BarangMovement::getBarangTidakLaku($bulanBatas);

        return view('laporan.tidak-laku', compact('barangTidakLaku', 'bulanBatas'));
    }

    // Helper: Summary Pembayaran
    private function getSummaryPembayaran($tanggalMulai, $tanggalAkhir)
    {
        $transaksis = BarangKeluar::with(['items'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get();

        $totalCash = 0;
        $totalQris = 0;
        $totalTransfer = 0;

        $jumlahTransaksiCash = 0;
        $jumlahTransaksiQris = 0;
        $jumlahTransaksiTransfer = 0;

        foreach ($transaksis as $transaksi) {
            $totalTransaksi = $transaksi->items->sum('total_harga');

            switch ($transaksi->metode_pembayaran) {
                case 'cash':
                    $totalCash += $totalTransaksi;
                    $jumlahTransaksiCash++;
                    break;
                case 'qris':
                    $totalQris += $totalTransaksi;
                    $jumlahTransaksiQris++;
                    break;
                case 'transfer':
                    $totalTransfer += $totalTransaksi;
                    $jumlahTransaksiTransfer++;
                    break;
            }
        }

        $totalPenjualan = $totalCash + $totalQris + $totalTransfer;

        return [
            'total_cash' => $totalCash,
            'total_qris' => $totalQris,
            'total_transfer' => $totalTransfer,
            'jumlah_cash' => $jumlahTransaksiCash,
            'jumlah_qris' => $jumlahTransaksiQris,
            'jumlah_transfer' => $jumlahTransaksiTransfer,
            'persentase_cash' => $totalPenjualan > 0 ? ($totalCash / $totalPenjualan) * 100 : 0,
            'persentase_qris' => $totalPenjualan > 0 ? ($totalQris / $totalPenjualan) * 100 : 0,
            'persentase_transfer' => $totalPenjualan > 0 ? ($totalTransfer / $totalPenjualan) * 100 : 0,
        ];
    }

    // PERBAIKAN: Method untuk menghitung total pembelian barang yang keluar
    // PERBAIKAN: Method untuk menghitung total pembelian barang yang keluar
    // Method untuk menghitung total pembelian barang yang keluar
    private function getTotalPembelianBarangKeluar($tanggalMulai, $tanggalAkhir)
    {
        $totalPembelian = 0;

        // Ambil semua transaksi keluar dalam periode
        $transaksis = BarangKeluar::with(['items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get();

        foreach ($transaksis as $transaksi) {
            foreach ($transaksi->items as $item) {
                if ($item->barang_id) {
                    $hargaBeli = $this->getHargaBeliBarang(
                        $item->barang_id,
                        $tanggalMulai,
                        $tanggalAkhir,
                        $item->jumlah_in_base_unit
                    );

                    $totalPembelian += $hargaBeli * $item->jumlah_in_base_unit;
                } else {
                    // Untuk barang manual
                    $totalPembelian += $item->total_harga * 0.8;
                }
            }
        }

        return $totalPembelian;
    }

    // Method getSummaryData
    private function getSummaryData($tanggalMulai, $tanggalAkhir)
    {
        // Total pembelian (hanya untuk barang yang keluar)
        $totalPembelianBarangKeluar = $this->getTotalPembelianBarangKeluar($tanggalMulai, $tanggalAkhir);

        // Total penjualan
        $totalPenjualan = BarangKeluarItem::whereHas('barangKeluar', function ($query) use ($tanggalMulai, $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);
        })->sum('total_harga');

        // Jumlah transaksi
        $jumlahTransaksiMasuk = BarangMasukTransaksi::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->count();
        $jumlahTransaksiKeluar = BarangKeluar::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])->count();

        // Hitung profit
        $profit = $totalPenjualan - $totalPembelianBarangKeluar;

        // Hitung persentase profit
        $profitPercentage = $totalPembelianBarangKeluar > 0
            ? ($profit / $totalPembelianBarangKeluar) * 100
            : ($totalPenjualan > 0 ? 100 : 0);

        return [
            'total_pembelian' => $totalPembelianBarangKeluar,
            'total_penjualan' => $totalPenjualan,
            'jumlah_transaksi_masuk' => $jumlahTransaksiMasuk,
            'jumlah_transaksi_keluar' => $jumlahTransaksiKeluar,
            'profit' => $profit,
            'profit_percentage' => $profitPercentage,
        ];
    }

    // Export PDF Laporan Harian - FIXED untuk multi-item
    public function exportPdf(Request $request): Response
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        if (!$tanggalMulai || !$tanggalAkhir) {
            return back()->with('error', 'Tanggal mulai dan akhir harus diisi');
        }

        $summary = $this->getSummaryData($tanggalMulai, $tanggalAkhir);
        $summaryPembayaran = $this->getSummaryPembayaran($tanggalMulai, $tanggalAkhir);

        // Data barang masuk
        $barangMasukTransaksis = BarangMasukTransaksi::with(['supplier', 'items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest('tanggal')
            ->get();

        // Data barang keluar
        $barangKeluarTransaksis = BarangKeluar::with(['customer', 'items.barang'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->latest('tanggal')
            ->get();

        // Top barang
        $topBarang = $this->getTopBarang($tanggalMulai, $tanggalAkhir);

        // Untuk kompatibilitas dengan PDF view lama
        $barangMasuks = $barangMasukTransaksis;
        $barangKeluars = $barangKeluarTransaksis;

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'tanggalMulai',
            'tanggalAkhir',
            'summary',
            'summaryPembayaran',
            'barangMasukTransaksis',
            'barangKeluarTransaksis',
            'barangMasuks',
            'barangKeluars',
            'topBarang'
        ));

        $filename = 'Laporan_Periodik_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    // Export PDF Laporan Bulanan
    public function exportBulananPdf(Request $request): Response
    {
        $tahun = $request->input('tahun', Carbon::now()->year);

        // DATA BULANAN
        $monthlyReports = MonthlyReport::where('tahun', $tahun)
            ->orderBy('bulan', 'asc')
            ->get();

        // TANGGAL CETAK
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        // SUMMARY TAHUNAN
        $totalPembelian = $monthlyReports->sum('total_pembelian');
        $totalPenjualan = $monthlyReports->sum('total_penjualan');
        $totalCash = $monthlyReports->sum('total_penjualan_cash');
        $totalQris = $monthlyReports->sum('total_penjualan_qris');
        $totalTransfer = $monthlyReports->sum('total_penjualan_transfer');
        $totalProfit = $monthlyReports->sum('profit');

        // TOP 10 BARANG TERLARIS
        $topBarang = $this->getTopBarang(
            Carbon::create($tahun, 1, 1)->format('Y-m-d'),
            Carbon::create($tahun, 12, 31)->format('Y-m-d')
        );

        $summary = [
            'total_pembelian' => $totalPembelian,
            'total_penjualan' => $totalPenjualan,
            'total_cash' => $totalCash,
            'total_qris' => $totalQris,
            'total_transfer' => $totalTransfer,
            'profit' => $totalProfit,
            'profit_percentage' => $totalPembelian > 0 ? ($totalProfit / $totalPembelian) * 100 : 0,
            'top_barang' => $topBarang,
        ];

        $pdf = Pdf::loadView('laporan.bulanan-pdf', compact(
            'tahun',
            'tanggalCetak',
            'monthlyReports',
            'summary'
        ))->setPaper('A4', 'landscape');

        return $pdf->download("Laporan_Bulanan_{$tahun}.pdf");
    }
}
