<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesee extends Model
{
    /** @use HasFactory<\Database\Factories\PeseeFactory> */
    use HasFactory;

    protected $table = 'pesees';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'user_id',
        'date_pesee',
        'nombre_sujets',
        'poids_moyen',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_pesee' => 'date',
            'poids_moyen' => 'decimal:3',
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
