<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2); // dalam base unit
            $table->decimal('stok_sebelum', 15, 2);
            $table->decimal('stok_sesudah', 15, 2);
            $table->string('referensi_tabel'); // barang_masuks atau barang_keluars
            $table->unsignedBigInteger('referensi_id');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index(['barang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
