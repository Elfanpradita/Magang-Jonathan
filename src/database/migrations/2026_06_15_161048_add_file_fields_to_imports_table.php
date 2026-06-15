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
        Schema::table('imports', function (Blueprint $table) {
            // Cek terlebih dahulu untuk menghindari crash jika kolom sudah terlanjur dibuat di terminal tadi Pak
            if (!Schema::hasColumn('imports', 'file_name')) {
                $table->string('file_name')->nullable()->after('importer');
            }
            
            if (!Schema::hasColumn('imports', 'file_path')) {
                $table->string('file_path')->nullable()->after('file_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_path']);
        });
    }
};