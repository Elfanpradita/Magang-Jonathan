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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Relasi bawaan sistem dashboard Filament
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            // 🚀 STRUKTUR MUTLAK 20 KOLOM FINAL EXCEL SESUAI KEINGINAN KLIEN
            $table->string('code_barang')->nullable();       // 1. Code Barang
            $table->text('nama_barang')->nullable();         // 2. Nama Barang
            $table->string('month')->nullable();             // 3. Month
            $table->string('vendor')->nullable();            // 4. Vendor
            $table->decimal('stock', 15, 2)->default(0);     // 5. Stock
            $table->decimal('harga', 15, 2)->default(0);     // 6. Harga
            $table->decimal('subtotal', 15, 2)->default(0);  // 7. Subtotal
            $table->text('keterangan')->nullable();          // 8. Keterangan
            $table->string('nomor_rak')->nullable();         // 9. Nomor Rak
            $table->string('kategori_excel')->nullable();    // 10. Kategori
            $table->string('type_excel')->nullable();        // 11. Type
            $table->string('unit')->nullable();              // 12. Unit
            $table->decimal('saldo_awal', 15, 2)->default(0); // 13. Saldo Awal
            $table->decimal('saldo_akhir', 15, 2)->default(0);// 14. Saldo Akhir
            $table->decimal('physical_stock', 15, 2)->default(0); // 15. Physical Stock
            $table->decimal('difference', 15, 2)->default(0);     // 16. Difference
            $table->text('remark')->nullable();              // 17. Remark
            $table->string('petugas_opname')->nullable();    // 18. Petugas Opname
            $table->string('status')->nullable();            // 19. Status
            $table->string('aksi')->nullable();              // 20. Aksi

            // Kolom utilitas sistem utama untuk keperluan chart tren operasional 2026
            $table->string('transaction_number')->nullable();
            $table->string('type')->default('expense'); 
            $table->date('date')->nullable();
            $table->foreignId('created_by_id')->nullable();
            $table->foreignId('approved_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};