<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukTransaksi extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk_transaksis';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'supplier_id',
        'invoice_supplier',
        'keterangan',
        'grand_total',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'grand_total' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(BarangMasukItem::class, 'barang_masuk_transaksi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Format tanggal
    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal->format('d/m/Y');
    }

    // Hitung jumlah item
    public function getJumlahItemAttribute()
    {
        return $this->items->count();
    }

    // Generate nomor transaksi yang unik
    public static function generateNoTransaksi()
    {
        $date = date('Ymd');
        $time = date('His');
        $random = bin2hex(random_bytes(3));

        return "BM-{$date}-{$time}-{$random}";
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $keyword)
    {
        return $query->where('no_transaksi', 'like', "%{$keyword}%")
            ->orWhereHas('supplier', function ($q) use ($keyword) {
                $q->where('nama_supplier', 'like', "%{$keyword}%");
            })
            ->orWhereHas('items.barang', function ($q) use ($keyword) {
                $q->where('nama_barang', 'like', "%{$keyword}%");
            });
    }
}
