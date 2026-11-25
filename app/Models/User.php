<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'email',
        'password',
        'actif',
    ];

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
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function exploitation(): BelongsTo
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function estResponsable(): bool
    {
        return $this->role === 'responsable';
    }

    public function estAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function libelleRole(): string
    {
        return match ($this->role) {
            'admin' => 'Administrateur',
            'responsable' => 'Responsable d\'exploitation',
            'agent' => 'Agent',
        };
    }
}
