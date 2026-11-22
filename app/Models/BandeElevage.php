<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BandeElevage extends Model
{
    /** @use HasFactory<\Database\Factories\BandeElevageFactory> */
    use HasFactory;

    protected $table = 'bandes_elevage';

    protected $fillable = [
        'exploitation_id',
        'poulailler_id',
        'production_id',
        'code',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'effectif_initial',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin_prevue' => 'date',
            'date_fin_reelle' => 'date',
        ];
    }

    public function exploitation(): BelongsTo
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function poulailler(): BelongsTo
    {
        return $this->belongsTo(Poulailler::class);
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }
}
