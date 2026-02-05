<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'nama_barang_manual',
        'jumlah',
        'unit_name',
        'jumlah_in_base_unit',
        'harga_jual',
        'total_harga',
    ];

    protected $casts = [
        'jumlah' => 'decimal:4',
        'jumlah_in_base_unit' => 'decimal:4',
        'harga_jual' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function stockHistory()
    {
        return $this->hasOne(StockHistory::class, 'referensi_id')
            ->where('referensi_tabel', 'barang_keluar_items');
    }

    // Hook untuk menghitung total harga
    protected static function booted()
    {
        static::creating(function ($item) {
            $item->total_harga = $item->jumlah * $item->harga_jual;
        });

        static::updating(function ($item) {
            $item->total_harga = $item->jumlah * $item->harga_jual;
        });
    }
}