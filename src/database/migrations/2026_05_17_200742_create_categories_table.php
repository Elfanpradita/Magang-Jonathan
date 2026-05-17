<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable(); // Menyimpan nama icon heroicons
            $table->string('color', 7)->nullable(); // Menyimpan kode HEX warna (contoh: #7f5af0)
            $table->string('type', 50)->nullable();  // 'essential', 'operational', 'variable', dll.
            
            // Relasi ke dirinya sendiri untuk Sub-Kategori (Self-referencing)
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};