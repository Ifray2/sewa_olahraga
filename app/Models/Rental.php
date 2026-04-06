<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    // ── Konstanta status ────────────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ACTIVE    = 'active';
    const STATUS_RETURNED  = 'returned';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'rental_code',
        'user_id',
        'equipment_id',
        'quantity',
        'start_date',
        'end_date',
        'duration_days',
        'total_price',
        'status',
        'notes',
        'handled_by',
        'confirmed_at',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'   => 'date',
            'end_date'     => 'date',
            'total_price'  => 'decimal:2',
            'confirmed_at' => 'datetime',
            'returned_at'  => 'datetime',
        ];
    }

    // ── Boot: generate rental_code otomatis ─────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (Rental $rental) {
            if (empty($rental->rental_code)) {
                $rental->rental_code = 'RENT-' . now()->format('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    3, '0', STR_PAD_LEFT
                );
            }
        });
    }

    // ── Scope ───────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ── Accessor ────────────────────────────────────────────────────────────
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING   => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_ACTIVE    => 'Sedang Disewa',
            self::STATUS_RETURNED  => 'Dikembalikan',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default                => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING   => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_ACTIVE    => 'green',
            self::STATUS_RETURNED  => 'gray',
            self::STATUS_CANCELLED => 'red',
            default                => 'gray',
        };
    }

    // ── Relasi ──────────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
} 