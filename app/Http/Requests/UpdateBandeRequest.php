<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('bande'));
    }

    public function rules(): array
    {
        $bande = $this->route('bande');
        $ferme = $this->user()->exploitation_id;

        if ($bande->statut === 'en_cours') {
            return [
                'date_fin_prevue' => ['required', 'date', 'after:' . $bande->date_debut->toDateString()],
            ];
        }

        return [
            'poulailler_id' => ['required', 'integer', Rule::exists('poulaillers', 'id')->where('exploitation_id', $ferme)],
            'production_id' => ['required', 'integer', Rule::exists('productions', 'id')->where('exploitation_id', $ferme)],
            'code' => [
                'required', 'string', 'max:30',
                Rule::unique('bandes_elevage', 'code')->where('exploitation_id', $ferme)->ignore($bande->id),
            ],
            'date_debut' => ['required', 'date'],
            'date_fin_prevue' => ['required', 'date', 'after:date_debut'],
            'effectif_initial' => ['required', 'integer', 'min:1', 'max:1000000'],
        ];
    }
}