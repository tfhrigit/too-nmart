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
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1-12
            $table->decimal('total_pembelian', 15, 2)->default(0);
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('total_penjualan_cash', 15, 2)->default(0);
            $table->decimal('total_penjualan_qris', 15, 2)->default(0);
            $table->decimal('total_penjualan_transfer', 15, 2)->default(0);
            $table->integer('jumlah_transaksi_masuk')->default(0);
            $table->integer('jumlah_transaksi_keluar')->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('profit_percentage', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
            $table->index(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
