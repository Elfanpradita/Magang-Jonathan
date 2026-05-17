<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThresholdRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'min_stock_critical',
        'min_stock_warning',
        'auto_reorder',
        'is_active',
    ];

    protected $casts = [
        'auto_reorder' => 'boolean',
        'is_active' => 'boolean',
        'min_stock_critical' => 'integer',
        'min_stock_warning' => 'integer',
    ];

    /**
     * Relasi balik ke Kategori terkait
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}