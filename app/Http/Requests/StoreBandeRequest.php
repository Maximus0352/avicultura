<?php

namespace App\Http\Requests;

use App\Models\BandeElevage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', BandeElevage::class);
    }

    public function rules(): array
    {
        $ferme = $this->user()->exploitation_id;

        return [
            'poulailler_id' => ['required', 'integer', Rule::exists('poulaillers', 'id')->where('exploitation_id', $ferme)],
            'production_id' => ['required', 'integer', Rule::exists('productions', 'id')->where('exploitation_id', $ferme)],
            'code' => ['required', 'string', 'max:30', Rule::unique('bandes_elevage', 'code')->where('exploitation_id', $ferme)],
            'date_debut' => ['required', 'date'],
            'date_fin_prevue' => ['required', 'date', 'after:date_debut'],
            'effectif_initial' => ['required', 'integer', 'min:1', 'max:1000000'],
        ];
    }
}