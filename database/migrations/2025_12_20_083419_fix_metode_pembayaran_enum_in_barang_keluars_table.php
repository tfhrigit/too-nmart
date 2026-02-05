<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek jika menggunakan MySQL
        if (DB::getDriverName() === 'mysql') {
            // Ubah kolom metode_pembayaran ke string terlebih dahulu
            DB::statement("ALTER TABLE barang_keluars MODIFY COLUMN metode_pembayaran VARCHAR(20) NOT NULL DEFAULT 'cash'");
            
            // Kembalikan ke enum dengan nilai yang benar
            DB::statement("ALTER TABLE barang_keluars MODIFY COLUMN metode_pembayaran ENUM('cash', 'qris', 'transfer') NOT NULL DEFAULT 'cash'");
        } else {
            // Untuk database lain (SQLite, PostgreSQL, dll)
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->dropColumn('metode_pembayaran');
            });
            
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->enum('metode_pembayaran', ['cash', 'qris', 'transfer'])
                      ->default('cash')
                      ->after('total_harga');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE barang_keluars MODIFY COLUMN metode_pembayaran VARCHAR(20) NOT NULL DEFAULT 'cash'");
            DB::statement("ALTER TABLE barang_keluars MODIFY COLUMN metode_pembayaran ENUM('cash', 'qris') NOT NULL DEFAULT 'cash'");
        } else {
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->dropColumn('metode_pembayaran');
            });
            
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->enum('metode_pembayaran', ['cash', 'qris'])
                      ->default('cash')
                      ->after('total_harga');
            });
        }
    }
};