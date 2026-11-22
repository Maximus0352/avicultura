<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationSanitaire extends Model
{
    /** @use HasFactory<\Database\Factories\ObservationSanitaireFactory> */
    use HasFactory;

    protected $table = 'observations_sanitaires';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'user_id',
        'contenu_chiffre',
        'date_observation',
    ];

    protected $hidden = [
        'contenu_chiffre',
    ];

    protected function casts(): array
    {
        return [
            'date_observation' => 'date',
        ];
    }

    public function bande(): BelongsTo
    {
        return $this->belongsTo(BandeElevage::class, 'bande_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
