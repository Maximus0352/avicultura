<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePoulaillerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('poulailler'));
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required', 'string', 'max:30',
                Rule::unique('poulaillers', 'code')
                    ->where('exploitation_id', $this->user()->exploitation_id)
                    ->ignore($this->route('poulailler')->id),
            ],
            'nom' => ['required', 'string', 'max:255'],
            'capacite' => ['required', 'integer', 'min:1', 'max:1000000'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'etat' => ['required', Rule::in(['actif', 'inactif', 'maintenance'])],
        ];
    }
}