<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Production extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionFactory> */
    use HasFactory;

    protected $table = 'productions';

    protected $fillable = [
        'exploitation_id',
        'nom',
        'description',
        'unite_production',
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
