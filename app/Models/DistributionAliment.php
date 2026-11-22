<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DistributionAliment extends Model
{
    /** @use HasFactory<\Database\Factories\DistributionAlimentFactory> */
    use HasFactory;

    protected $table = 'distributions_aliment';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'intrant_id',
        'user_id',
        'date_distribution',
        'quantite',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_distribution' => 'date',
            'quantite' => 'decimal:3',
        ];
    }

    public function bande(): BelongsTo
    {
        return $this->belongsTo(BandeElevage::class, 'bande_id');
    }

    public function intrant(): BelongsTo
    {
        return $this->belongsTo(Intrant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mouvement(): HasOne
    {
        return $this->hasOne(MouvementStock::class, 'distribution_id');
    }
}
