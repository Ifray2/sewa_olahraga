<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'brand',
        'price_per_day',
        'stock',
        'condition',
        'image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'is_available'  => 'boolean',
        ];
    }

    // ── Scope ───────────────────────────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    // ── Accessor ────────────────────────────────────────────────────────────

    /** Format harga ke Rupiah, misal: "Rp 150.000" */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_day, 0, ',', '.');
    }

    /** Rating rata-rata dari semua review */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    // ── Relasi ──────────────────────────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}