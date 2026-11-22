<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BandeElevage extends Model
{
    /** @use HasFactory<\Database\Factories\BandeElevageFactory> */
    use HasFactory, AppartientAUneExploitation;

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

        public function mortalites(): HasMany
    {
        return $this->hasMany(Mortalite::class, 'bande_id');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class, 'bande_id');
    }

    public function pesees(): HasMany
    {
        return $this->hasMany(Pesee::class, 'bande_id');
    }

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class, 'bande_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(DistributionAliment::class, 'bande_id');
    }
}
