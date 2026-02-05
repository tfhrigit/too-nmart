<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 


/**
 * @property int $id
 * @property string $no_transaksi
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int|null $customer_id
 * @property string|null $metode_pembayaran
 * @property string|null $keterangan
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangKeluarItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class BarangKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'customer_id',
        'metode_pembayaran',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relationship dengan items
    public function items()
    {
        return $this->hasMany(BarangKeluarItem::class, 'barang_keluar_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi barang melalui items
    public function barangs()
    {
        return $this->hasManyThrough(
            Barang::class,
            BarangKeluarItem::class,
            'barang_keluar_id',
            'id',
            'id',
            'barang_id'
        );
    }

    public static function generateNoTransaksi()
    {
        $date = date('Ymd');
        $lastTransaction = self::whereDate('created_at', today())->latest('id')->first();
        $lastNumber = $lastTransaction ? intval(substr($lastTransaction->no_transaksi, -4)) : 0;
        return 'BK-' . $date . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getMetodePembayaranLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'qris' => 'QRIS',
            'transfer' => 'Transfer'
        ];

        return $labels[$this->metode_pembayaran] ?? $this->metode_pembayaran;
    }

    public function getMetodePembayaranBadgeAttribute()
    {
        $badges = [
            'cash' => 'bg-success',
            'qris' => 'bg-primary',
            'transfer' => 'bg-info'
        ];

        $color = $badges[$this->metode_pembayaran] ?? 'bg-secondary';

        return '<span class="badge ' . $color . '">' . $this->metode_pembayaran_label . '</span>';
    }

    // Menghitung total transaksi (FIXED: sum dari items)
    public function getTotalTransaksiAttribute()
    {
        return $this->items()->sum('total_harga');
    }

    // Menghitung jumlah item
    public function getJumlahItemAttribute()
    {
        return $this->items()->count();
    }
}