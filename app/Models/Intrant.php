<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intrant extends Model
{
    /** @use HasFactory<\Database\Factories\IntrantFactory> */
    use HasFactory;
    
    protected $table = 'intrants';

    protected $fillable = [
        'exploitation_id',
        'nom',
        'categorie',
        'unite',
        'stock_actuel',
        'seuil_critique',
        'prix_unitaire',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'stock_actuel' => 'decimal:3',
            'seuil_critique' => 'decimal:3',
            'prix_unitaire' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function exploitation(): BelongsTo
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function alertes(): HasMany
    {
        return $this->hasMany(Alerte::class);
    }

    public function estAliment(): bool
    {
        return $this->categorie === 'aliment';
    }
}
