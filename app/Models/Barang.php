<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode_barang
 * @property string $nama_barang
 * @property string $base_unit
 * @property numeric $stok_sekarang
 * @property int $stok_minimum
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangKeluar> $barangKeluars
 * @property-read int|null $barang_keluars_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangMasuk> $barangMasuks
 * @property-read int|null $barang_masuks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemUnit> $itemUnits
 * @property-read int|null $item_units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockHistory> $stockHistories
 * @property-read int|null $stock_histories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereBaseUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereKodeBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereStokMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereStokSekarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'base_unit',
        'stok_sekarang',
        'stok_minimum',
        'deskripsi',
        'harga_beli',
        'harga_jual',
    ];

    protected $casts = [
        'stok_sekarang' => 'decimal:2',
        'stok_minimum' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    // PERBAIKAN: Relasi barangKeluars harus melalui barangKeluarItems
    public function barangKeluars()
    {
        return $this->hasManyThrough(
            BarangKeluar::class, // Model target
            BarangKeluarItem::class, // Model perantara
            'barang_id', // Foreign key pada tabel perantara
            'id', // Foreign key pada tabel target
            'id', // Local key pada model Barang
            'barang_keluar_id' // Local key pada model perantara
        );
    }

    // Relasi langsung ke barangKeluarItems
    public function barangKeluarItems()
    {
        return $this->hasMany(BarangKeluarItem::class, 'barang_id');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getLastHargaBeliAttribute()
    {
        $lastBarangMasuk = $this->barangMasuks()
            ->with('supplier')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastBarangMasuk ? $lastBarangMasuk->harga_beli : null;
    }

    // Get supplier terakhir
    public function getLastSupplierAttribute()
    {
        $lastBarangMasuk = $this->barangMasuks()
            ->with('supplier')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastBarangMasuk ? $lastBarangMasuk->supplier : null;
    }

    public static function generateKode()
    {
        $lastBarang = self::latest('id')->first();
        $lastNumber = $lastBarang ? intval(substr($lastBarang->kode_barang, 4)) : 0;
        return 'BRG-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function isStokKritis()
    {
        return $this->stok_sekarang <= $this->stok_minimum;
    }

    public function isStokHabis()
    {
        return $this->stok_sekarang <= 0;
    }

    public function convertToBaseUnit($jumlah, $unitName)
    {
        try {
            // Jika unit sama dengan base unit
            if ($unitName === $this->base_unit) {
                return $jumlah;
            }

            // Cari di itemUnits
            $itemUnit = $this->itemUnits->where('unit_name', $unitName)->first();

            if ($itemUnit) {
                // Gunakan multiplier jika ada
                if (isset($itemUnit->multiplier) && $itemUnit->multiplier > 0) {
                    return $jumlah * $itemUnit->multiplier;
                }

                // Atau gunakan conversion_factor
                if (isset($itemUnit->conversion_factor) && $itemUnit->conversion_factor > 0) {
                    return $jumlah * $itemUnit->conversion_factor;
                }
            }

            // Default: anggap 1:1
            \Log::warning("Unit {$unitName} tidak ditemukan untuk barang {$this->nama_barang}, menggunakan default 1:1");
            return $jumlah;
        } catch (\Exception $e) {
            \Log::error("Error convertToBaseUnit: " . $e->getMessage());
            return $jumlah; // Default ke nilai asli
        }
    }

    public function convertFromBaseUnit($jumlahBase, $unitName)
    {
        if ($unitName === $this->base_unit) {
            return $jumlahBase;
        }

        $unit = $this->itemUnits()->where('unit_name', $unitName)->first();
        if (!$unit) {
            throw new \Exception("Unit {$unitName} tidak ditemukan untuk barang {$this->nama_barang}");
        }

        return $jumlahBase / $unit->multiplier;
    }

    // Method untuk mendapatkan total barang keluar
    public function getTotalBarangKeluarAttribute()
    {
        return $this->barangKeluarItems()->sum('jumlah_in_base_unit');
    }

    // Method untuk mendapatkan total barang masuk
    public function getTotalBarangMasukAttribute()
    {
        return $this->barangMasuks()->sum('jumlah');
    }

    // Method untuk mendapatkan nilai total penjualan
    public function getTotalPenjualanAttribute()
    {
        return $this->barangKeluarItems()->sum('total_harga');
    }

    // Method untuk mendapatkan nilai total pembelian
    public function getTotalPembelianAttribute()
    {
        return $this->barangMasuks()->sum('total_harga');
    }
}