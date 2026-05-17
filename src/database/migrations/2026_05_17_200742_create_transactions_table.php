<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel categories
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('vendor', 255)->nullable();
            $table->string('transaction_number', 50)->unique(); // Format otomatis (e.g., TXN-2026001)
            $table->string('type', 20); // 'expense', 'income', 'transfer'
            $table->string('description', 500);
            $table->decimal('amount', 15, 2); // Presisi uang rapi
            $table->date('date');
            $table->string('status', 20)->default('pending'); // 'pending', 'approved', 'rejected'
            $table->text('notes')->nullable();

            // Audit Trail - Menghubungkan ke tabel 'users' bawaan Laravel
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};