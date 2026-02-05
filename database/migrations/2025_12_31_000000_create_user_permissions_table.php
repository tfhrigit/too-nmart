<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('permission');
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['user_id', 'permission']);
            
            // Index untuk query cepat
            $table->index('permission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
