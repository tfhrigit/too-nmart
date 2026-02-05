<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode_supplier
 * @property string $nama_supplier
 * @property string|null $alamat
 * @property string|null $no_hp
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangMasuk> $barangMasuks
 * @property-read int|null $barang_masuks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereKodeSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNamaSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
// Supplier.php - tambahkan relationship baru
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'no_hp',
        'email',
    ];

    // Relationship lama (untuk kompatibilitas)
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }
    
    // Relationship baru untuk transaksi barang masuk
    public function barangMasukTransaksis()
    {
        return $this->hasMany(BarangMasukTransaksi::class);
    }

    public static function generateKode()
    {
        $lastSupplier = self::latest('id')->first();
        $lastNumber = $lastSupplier ? intval(substr($lastSupplier->kode_supplier, 4)) : 0;
        return 'SUP-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }
}