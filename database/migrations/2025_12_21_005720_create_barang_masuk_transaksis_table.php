// database/migrations/xxxx_create_barang_masuk_transaksis_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuk_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('invoice_supplier')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barang_masuk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_transaksi_id')->constrained('barang_masuk_transaksis')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->decimal('jumlah', 12, 4);
            $table->string('unit_name');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_items');
        Schema::dropIfExists('barang_masuk_transaksis');
    }
};