<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'tahun',
        'bulan',
        'total_masuk',
        'total_keluar',
        'nilai_masuk',
        'nilai_keluar',
        'frekuensi_masuk',
        'frekuensi_keluar',
        'last_keluar_date',
        'hari_tidak_terjual',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'total_masuk' => 'decimal:2',
        'total_keluar' => 'decimal:2',
        'nilai_masuk' => 'decimal:2',
        'nilai_keluar' => 'decimal:2',
        'last_keluar_date' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function getBulanNameAttribute()
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$this->bulan] ?? '';
    }

    public static function updateMovement($barangId, $tahun, $bulan)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Data Barang Masuk
        $dataMasuk = \App\Models\BarangMasuk::where('barang_id', $barangId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(jumlah) as total_masuk, SUM(total_harga) as nilai_masuk, COUNT(*) as frekuensi')
            ->first();

        // Data Barang Keluar - JOIN dengan barang_keluar_items karena kolom jumlah ada di sana
        $dataKeluar = \App\Models\BarangKeluarItem::join('barang_keluars', 'barang_keluar_items.barang_keluar_id', '=', 'barang_keluars.id')
            ->where('barang_keluar_items.barang_id', $barangId)
            ->whereBetween('barang_keluars.tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(barang_keluar_items.jumlah) as total_keluar, SUM(barang_keluar_items.total_harga) as nilai_keluar, COUNT(DISTINCT barang_keluars.id) as frekuensi, MAX(barang_keluars.tanggal) as last_date')
            ->first();

        // Hitung hari tidak terjual
        $lastKeluarDate = $dataKeluar->last_date ? Carbon::parse($dataKeluar->last_date) : null;
        $hariTidakTerjual = 0;
        
        if ($lastKeluarDate) {
            $hariTidakTerjual = $lastKeluarDate->diffInDays(now());
        } else {
            // Jika tidak pernah terjual, hitung dari tanggal barang dibuat
            $barang = \App\Models\Barang::find($barangId);
            if ($barang) {
                $hariTidakTerjual = $barang->created_at->diffInDays(now());
            }
        }

        return self::updateOrCreate(
            ['barang_id' => $barangId, 'tahun' => $tahun, 'bulan' => $bulan],
            [
                'total_masuk' => $dataMasuk->total_masuk ?? 0,
                'total_keluar' => $dataKeluar->total_keluar ?? 0,
                'nilai_masuk' => $dataMasuk->nilai_masuk ?? 0,
                'nilai_keluar' => $dataKeluar->nilai_keluar ?? 0,
                'frekuensi_masuk' => $dataMasuk->frekuensi ?? 0,
                'frekuensi_keluar' => $dataKeluar->frekuensi ?? 0,
                'last_keluar_date' => $lastKeluarDate,
                'hari_tidak_terjual' => $hariTidakTerjual,
            ]
        );
    }

    public static function getBarangTidakLaku($bulanBatas = 3)
    {
        $hariBatas = $bulanBatas * 30;
        
        return self::with('barang')
            ->whereHas('barang', function($query) {
                $query->where('stok_sekarang', '>', 0);
            })
            ->where('hari_tidak_terjual', '>=', $hariBatas)
            ->orderBy('hari_tidak_terjual', 'desc')
            ->get();
    }
}
