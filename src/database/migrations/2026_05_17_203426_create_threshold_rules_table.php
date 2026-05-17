<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threshold_rules', function (Blueprint $table) {
            $table->id();
            // Menghubungkan rule ke kategori tertentu (Satu kategori hanya boleh punya satu rule utama)
            $table->foreignId('category_id')->unique()->constrained()->cascadeOnDelete();
            
            $table->integer('min_stock_critical')->default(5);
            $table->integer('min_stock_warning')->default(10);
            $table->boolean('auto_reorder')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threshold_rules');
    }
};