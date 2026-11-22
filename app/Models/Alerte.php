<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
    /** @use HasFactory<\Database\Factories\AlerteFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'alertes';

    protected $fillable = [
        'exploitation_id',
        'intrant_id',
        'type',
        'message',
        'statut',
        'date_alerte',
        'date_resolution',
    ];

    protected function casts(): array
    {
        return [
            'date_alerte' => 'datetime',
            'date_resolution' => 'datetime',
        ];
    }

    public function intrant(): BelongsTo
    {
        return $this->belongsTo(Intrant::class);
    }
}
