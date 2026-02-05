<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $barang_id
 * @property string $unit_name
 * @property numeric $multiplier
 * @property bool $is_base
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Barang $barang
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereBarangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereIsBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemUnit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'unit_name',
        'multiplier',
        'is_base',
    ];

    protected $casts = [
        'multiplier' => 'decimal:2',
        'is_base' => 'boolean',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
