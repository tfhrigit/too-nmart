<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'total_pembelian',
        'total_penjualan',
        'total_penjualan_cash',
        'total_penjualan_qris',
        'total_penjualan_transfer',
        'jumlah_transaksi_masuk',
        'jumlah_transaksi_keluar',
        'profit',
        'profit_percentage',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'total_pembelian' => 'decimal:2',
        'total_penjualan' => 'decimal:2',
        'total_penjualan_cash' => 'decimal:2',
        'total_penjualan_qris' => 'decimal:2',
        'total_penjualan_transfer' => 'decimal:2',
        'profit' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
    ];

    public function getBulanNameAttribute()
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$this->bulan] ?? '';
    }

    public static function generateReport($tahun, $bulan)
    {
        $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $totalPembelian = \App\Models\BarangMasuk::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_harga');

        $totalPenjualan = \App\Models\BarangKeluar::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_harga');

        $totalPenjualanCash = \App\Models\BarangKeluar::whereBetween('tanggal', [$startDate, $endDate])
            ->where('metode_pembayaran', 'cash')
            ->sum('total_harga');

        $totalPenjualanQris = \App\Models\BarangKeluar::whereBetween('tanggal', [$startDate, $endDate])
            ->where('metode_pembayaran', 'qris')
            ->sum('total_harga');

        $jumlahTransaksiMasuk = \App\Models\BarangMasuk::whereBetween('tanggal', [$startDate, $endDate])->count();
        $jumlahTransaksiKeluar = \App\Models\BarangKeluar::whereBetween('tanggal', [$startDate, $endDate])->count();

        $profit = $totalPenjualan - $totalPembelian;
        $profitPercentage = $totalPembelian > 0 ? ($profit / $totalPembelian) * 100 : 0;

        return self::updateOrCreate(
            ['tahun' => $tahun, 'bulan' => $bulan],
            [
                'total_pembelian' => $totalPembelian,
                'total_penjualan' => $totalPenjualan,
                'total_penjualan_cash' => $totalPenjualanCash,
                'total_penjualan_qris' => $totalPenjualanQris,
                'jumlah_transaksi_masuk' => $jumlahTransaksiMasuk,
                'jumlah_transaksi_keluar' => $jumlahTransaksiKeluar,
                'profit' => $profit,
                'profit_percentage' => $profitPercentage,
            ]
        );
    }
}