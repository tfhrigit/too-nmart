<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('unit_name'); // sak, box, karton, dll
            $table->decimal('multiplier', 10, 2); // 1 sak = 50 kg
            $table->boolean('is_base')->default(false); // apakah ini base unit
            $table->timestamps();
            
            $table->unique(['barang_id', 'unit_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
