<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $barang_id
 * @property string $jenis_transaksi
 * @property numeric $jumlah
 * @property numeric $stok_sebelum
 * @property numeric $stok_sesudah
 * @property string $referensi_tabel
 * @property int $referensi_id
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Barang $barang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereBarangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereJenisTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereReferensiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereReferensiTabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereStokSebelum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereStokSesudah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'barang_id',
        'jenis_transaksi',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi_tabel',
        'referensi_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // Method untuk mendapatkan label jenis transaksi
    public function getJenisTransaksiLabelAttribute()
    {
        return $this->jenis_transaksi === 'masuk' ? 'Barang Masuk' : 'Barang Keluar';
    }
    
    // Method untuk mendapatkan warna berdasarkan jenis transaksi
    public function getJenisTransaksiColorAttribute()
    {
        return $this->jenis_transaksi === 'masuk' ? 'success' : 'danger';
    }
}
