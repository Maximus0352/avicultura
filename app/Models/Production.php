<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Production extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'productions';

    protected $fillable = [
        'exploitation_id',
        'nom',
        'description',
        'unite_production',
    ];

    public function bandes(): HasMany
    {
        return $this->hasMany(BandeElevage::class);
    }
}
