<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activite extends Model
{
    /** @use HasFactory<\Database\Factories\ActiviteFactory> */
    use HasFactory;

    protected $table = 'activites';

    protected $fillable = [
        'exploitation_id',
        'bande_id',
        'user_id',
        'type',
        'date_activite',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date_activite' => 'date',
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
