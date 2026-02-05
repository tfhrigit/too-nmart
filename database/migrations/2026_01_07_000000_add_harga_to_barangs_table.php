<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'harga_beli')) {
                $table->decimal('harga_beli', 15, 2)->default(0)->after('deskripsi')->comment('Harga beli (0 = input manual)');
            }
            if (!Schema::hasColumn('barangs', 'harga_jual')) {
                $table->decimal('harga_jual', 15, 2)->default(0)->after('harga_beli')->comment('Harga jual (0 = input manual)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (Schema::hasColumn('barangs', 'harga_beli')) {
                $table->dropColumn('harga_beli');
            }
            if (Schema::hasColumn('barangs', 'harga_jual')) {
                $table->dropColumn('harga_jual');
            }
        });
    }
};
