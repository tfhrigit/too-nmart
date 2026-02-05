<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode_customer
 * @property string $nama_customer
 * @property string|null $no_hp
 * @property string|null $alamat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangKeluar> $barangKeluars
 * @property-read int|null $barang_keluars_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereKodeCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNamaCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_customer',
        'nama_customer',
        'no_hp',
        'alamat',
    ];

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public static function generateKode()
    {
        $lastCustomer = self::latest('id')->first();
        $lastNumber = $lastCustomer ? intval(substr($lastCustomer->kode_customer, 4)) : 0;
        return 'CST-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }
}