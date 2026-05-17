<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'vendor',
        'transaction_number',
        'type',
        'description',
        'amount',
        'date',
        'status',
        'notes',
        'created_by_id',
        'approved_by_id',
        'approved_at',
        'rejected_by_id',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * Relasi ke Kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke banyak berkas lampiran (Kwitansi/Invoice)
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TransactionAttachment::class);
    }

    /**
     * Audit Trail: User yang menginput transaksi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Audit Trail: User (Admin/Finance) yang menyetujui transaksi
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Audit Trail: User yang menolak transaksi
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }
}