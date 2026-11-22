<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mortalite extends Model
{
    /** @use HasFactory<\Database\Factories\MortaliteFactory> */
    use HasFactory;

    protected $table = 'mortalites';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'user_id',
        'date_mortalite',
        'quantite',
        'motif',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_mortalite' => 'date',
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
