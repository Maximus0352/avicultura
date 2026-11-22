<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationFinanciere extends Model
{
    /** @use HasFactory<\Database\Factories\OperationFinanciereFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'operations_financieres';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'activite_id',
        'vente_id',
        'user_id',
        'type',
        'categorie',
        'montant',
        'date_operation',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date_operation' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function bande(): BelongsTo
    {
        return $this->belongsTo(BandeElevage::class, 'bande_id');
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
