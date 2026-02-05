<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->decimal('total_masuk', 15, 2)->default(0);
            $table->decimal('total_keluar', 15, 2)->default(0);
            $table->decimal('nilai_masuk', 15, 2)->default(0);
            $table->decimal('nilai_keluar', 15, 2)->default(0);
            $table->integer('frekuensi_masuk')->default(0);
            $table->integer('frekuensi_keluar')->default(0);
            $table->date('last_keluar_date')->nullable();
            $table->integer('hari_tidak_terjual')->default(0);
            $table->timestamps();
            
            $table->unique(['barang_id', 'tahun', 'bulan']);
            $table->index(['barang_id', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_movements');
    }
};
