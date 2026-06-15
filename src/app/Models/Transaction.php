<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Properti Fillable Mass Assignment untuk 20 Kolom Mutlak Klien
     * + Utilitas Sistem Pendukung Dashboard Utama
     */
    protected $fillable = [
        'category_id',
        'transaction_number',
        'type',
        'date',
        'created_by_id',
        'approved_by_id',
        
        // 🚀 STRUKTUR MASALAH ULANG: 20 Kolom Final Excel
        'code_barang',       // 1. Code Barang
        'nama_barang',       // 2. Nama Barang
        'month',             // 3. Month
        'vendor',            // 4. Vendor
        'stock',             // 5. Stock
        'harga',             // 6. Harga
        'subtotal',          // 7. Subtotal
        'keterangan',        // 8. Keterangan
        'nomor_rak',         // 9. Nomor Rak
        'kategori_excel',    // 10. Kategori
        'type_excel',        // 11. Type
        'unit',              // 12. Unit
        'saldo_awal',        // 13. Saldo Awal
        'saldo_akhir',       // 14. Saldo Akhir
        'physical_stock',    // 15. Physical Stock
        'difference',        // 16. Difference
        'remark',            // 17. Remark
        'petugas_opname',    // 18. Petugas Opname
        'status',            // 19. Status
        'aksi'               // 20. Aksi
    ];

    /**
     * Casting Tipe Data Guna Menghindari Kerusakan Format Desimal (Decimal/Float)
     */
    protected $casts = [
        'date' => 'date',
        'stock' => 'decimal:2',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'saldo_awal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
        'physical_stock' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    /**
     * Relasi ke Tabel Kategori Master (AI Detected Classification)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}