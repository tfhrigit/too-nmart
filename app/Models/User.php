<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangKeluar> $barangKeluars
 * @property-read int|null $barang_keluars_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarangMasuk> $barangMasuks
 * @property-read int|null $barang_masuks_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Role constants
    const ROLE_OWNER = 'owner';
    const ROLE_KASIR = 'kasir';
    const ROLE_STAFF_GUDANG = 'staff_gudang';

    // Role methods
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function hasAccess(string $permission): bool
    {
        return $this->hasPermission($permission);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'owner') {
            return true;
        }

        return in_array($permission, $this->getPermissions());
    }


    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    public function isStaffGudang(): bool
    {
        return $this->role === self::ROLE_STAFF_GUDANG;
    }

    // Relasi ke UserPermission
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_OWNER => 'Owner/Admin',
            self::ROLE_KASIR => 'Kasir',
            self::ROLE_STAFF_GUDANG => 'Staff Gudang',
            default => $this->role,
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_OWNER => 'badge bg-danger',
            self::ROLE_KASIR => 'badge bg-primary',
            self::ROLE_STAFF_GUDANG => 'badge bg-info',
            default => 'badge bg-secondary',
        };
    }



    // Permission methods
    public function canAccess(string $permission): bool
    {
        // Owner punya akses penuh
        if ($this->role === self::ROLE_OWNER) {
            return true;
        }

        // Cek permission dari database
        return $this->userPermissions()
            ->where('permission', $permission)
            ->exists();
    }

    public function getPermissions(): array
    {
        // Owner punya semua permission
        if ($this->role === self::ROLE_OWNER) {
            return [
                // Dashboard
                'view_dashboard',

                // Data Barang
                'view_barang', 'create_barang', 'edit_barang', 'delete_barang',

                // Barang Masuk
                'view_barang_masuk', 'create_barang_masuk', 'edit_barang_masuk', 'delete_barang_masuk',

                // Barang Keluar
                'view_barang_keluar', 'create_barang_keluar', 'edit_barang_keluar', 'delete_barang_keluar',

                // Supplier
                'view_supplier', 'create_supplier', 'edit_supplier', 'delete_supplier',

                // Customer
                'view_customer', 'create_customer', 'edit_customer', 'delete_customer',

                // Laporan
                'view_laporan_transaksi', 'view_laporan_bulanan', 'view_laporan_pergerakan_barang', 'view_laporan_barang_tidak_laku',

                // User Management
                'view_users', 'create_users', 'edit_users', 'delete_users',
            ];
        }

        // Ambil permission dari database untuk non-owner
        return $this->userPermissions()
            ->pluck('permission')
            ->toArray();
    }

    // Daftar semua permission yang tersedia
    public static function getAllPermissions(): array
    {
        return [
            // Dashboard
            'view_dashboard' => 'Lihat Dashboard',

            // Data Barang
            'view_barang' => 'Lihat Data Barang',
            'create_barang' => 'Tambah Barang',
            'edit_barang' => 'Edit Barang',
            'delete_barang' => 'Hapus Barang',

            // Barang Masuk
            'view_barang_masuk' => 'Lihat Barang Masuk',
            'create_barang_masuk' => 'Tambah Barang Masuk',
            'edit_barang_masuk' => 'Edit Barang Masuk',
            'delete_barang_masuk' => 'Hapus Barang Masuk',

            // Barang Keluar
            'view_barang_keluar' => 'Lihat Barang Keluar',
            'create_barang_keluar' => 'Tambah Barang Keluar',
            'edit_barang_keluar' => 'Edit Barang Keluar',
            'delete_barang_keluar' => 'Hapus Barang Keluar',

            // Supplier
            'view_supplier' => 'Lihat Supplier',
            'create_supplier' => 'Tambah Supplier',
            'edit_supplier' => 'Edit Supplier',
            'delete_supplier' => 'Hapus Supplier',

            // Customer
            'view_customer' => 'Lihat Customer',
            'create_customer' => 'Tambah Customer',
            'edit_customer' => 'Edit Customer',
            'delete_customer' => 'Hapus Customer',

            // Laporan
            'view_laporan_transaksi' => 'Laporan Transaksi',
            'view_laporan_bulanan' => 'Laporan Bulanan',
            'view_laporan_pergerakan_barang' => 'Laporan Pergerakan Barang',
            'view_laporan_barang_tidak_laku' => 'Laporan Barang Tidak Laku',

            // User Management
            'view_users' => 'Lihat User Management',
            'create_users' => 'Tambah User',
            'edit_users' => 'Edit User',
            'delete_users' => 'Hapus User',
        ];
    }
}
