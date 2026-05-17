<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_attachments', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel transactions (jika transaksi dihapus, file ikut terhapus logis)
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            
            $table->text('file_url');
            $table->string('filename', 255);
            $table->string('file_type', 50)->nullable();
            $table->integer('file_size')->nullable(); // Dalam hitungan bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_attachments');
    }
};