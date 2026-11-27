<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TerminerBandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('changerStatut', $this->route('bande'));
    }

    public function rules(): array
    {
        return [
            'date_fin_reelle' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_fin_reelle.before_or_equal' => 'La date de fin réelle ne peut pas être dans le futur.',
        ];
    }
}