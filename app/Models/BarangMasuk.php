<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * @property int $id
 * @property string $no_transaksi
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $supplier_id
 * @property int $barang_id
 * @property numeric $jumlah
 * @property string $unit_name
 * @property numeric $harga_beli
 * @property numeric $total_harga
 * @property string|null $keterangan
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Barang $barang
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereBarangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereHargaBeli($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereNoTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereTotalHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangMasuk whereUserId($value)
 * @mixin \Eloquent
 */
class BarangMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'supplier_id',
        'barang_id',
        'jumlah',
        'unit_name',
        'harga_beli',
        'total_harga',
        'invoice_supplier',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->format('d/m/Y');
    }


    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockHistory()
    {
        return $this->hasOne(StockHistory::class, 'referensi_id')
            ->where('referensi_tabel', 'barang_masuks');
    }

    public function scopeByTransaction($query, $noTransaksi)
    {
        return $query->where('no_transaksi', $noTransaksi);
    }

    public static function getTotalByTransaction($noTransaksi)
    {
        return self::where('no_transaksi', $noTransaksi)
            ->sum('total_harga');
    }
}
