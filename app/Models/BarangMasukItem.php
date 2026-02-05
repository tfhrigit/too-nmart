<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukItem extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk_items';
    
    protected $fillable = [
        'barang_masuk_transaksi_id',
        'barang_id',
        'jumlah',
        'unit_name',
        'harga_beli',
        'total_harga',
    ];

    protected $casts = [
        'jumlah' => 'decimal:4',
        'harga_beli' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(BarangMasukTransaksi::class, 'barang_masuk_transaksi_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'referensi_id')
            ->where('referensi_tabel', 'barang_masuk_items');
    }
}