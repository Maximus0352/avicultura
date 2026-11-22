<?php

namespace App\Models;

use App\Models\Concerns\AppartientAUneExploitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueModification extends Model
{
    /** @use HasFactory<\Database\Factories\HistoriqueModificationFactory> */
    use HasFactory, AppartientAUneExploitation;

    protected $table = 'historiques_modifications';

    public $timestamps = false;

    protected $fillable = [
        'exploitation_id',
        'user_id',
        'table_concernee',
        'enregistrement_id',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'date_modification',
    ];

    protected function casts(): array
    {
        return [
            'anciennes_valeurs' => 'array',
            'nouvelles_valeurs' => 'array',
            'date_modification' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}