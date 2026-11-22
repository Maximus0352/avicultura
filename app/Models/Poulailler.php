<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poulailler extends Model
{
    /** @use HasFactory<\Database\Factories\PoulaillerFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'poulaillers';

    protected $fillable = [
        'exploitation_id',
        'code',
        'nom',
        'capacite',
        'localisation',
        'etat',
    ];

    public function bandes(): HasMany
    {
        return $this->hasMany(BandeElevage::class);
    }
}
