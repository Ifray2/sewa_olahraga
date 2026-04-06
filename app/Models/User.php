<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    const ROLE_ADMIN   = 'admin';
    const ROLE_PETUGAS = 'petugas';
    const ROLE_USER    = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Helpers role ────────────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PETUGAS]);
    }

    /** Semua rental milik user ini */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /** Rental yang ditangani oleh petugas/admin ini */
    public function handledRentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'handled_by');
    }

    /** Review yang ditulis user ini */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
