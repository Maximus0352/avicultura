<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poulailler extends Model
{
    /** @use HasFactory<\Database\Factories\PoulaillerFactory> */
    use HasFactory;

    protected $table = 'poulaillers';

    protected $fillable = [
        'exploitation_id',
        'code',
        'nom',
        'capacite',
        'localisation',
        'etat',
    ];

    public function exploitation(): BelongsTo
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function bandes(): HasMany
    {
        return $this->hasMany(BandeElevage::class);
    }
}
