<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const STATUS_UNPAID   = 'unpaid';
    const STATUS_PAID     = 'paid';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'rental_id',
        'amount',
        'method',
        'status',
        'proof_image',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // ── Scope ───────────────────────────────────────────────────────────────
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    // ── Accessor ────────────────────────────────────────────────────────────
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'transfer' => 'Transfer Bank',
            'cash'     => 'Tunai',
            'ewallet'  => 'E-Wallet',
            default    => ucfirst($this->method),
        };
    }

    // ── Relasi ──────────────────────────────────────────────────────────────
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}