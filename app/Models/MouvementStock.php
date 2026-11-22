<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    /** @use HasFactory<\Database\Factories\MouvementStockFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'exploitation_id',
        'intrant_id',
        'user_id',
        'type',
        'sens_ajustement',
        'quantite',
        'motif',
        'distribution_id',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'decimal:3',
        ];
    }

    public function intrant(): BelongsTo
    {
        return $this->belongsTo(Intrant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(DistributionAliment::class, 'distribution_id');
    }
}
