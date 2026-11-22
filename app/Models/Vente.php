<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vente extends Model
{
    /** @use HasFactory<\Database\Factories\VenteFactory> */
    use HasFactory;

    protected $table = 'ventes';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'user_id',
        'date_vente',
        'nombre_sujets',
        'montant_total',
    ];

    protected function casts(): array
    {
        return [
            'date_vente' => 'date',
            'montant_total' => 'decimal:2',
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

    public function recette(): HasOne
    {
        return $this->hasOne(OperationFinanciere::class, 'vente_id');
    }
}
