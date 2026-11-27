<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('production'));
    }

    public function rules(): array
    {
        return [
            'nom' => [
                'required', 'string', 'max:255',
                Rule::unique('productions', 'nom')
                    ->where('exploitation_id', $this->user()->exploitation_id)
                    ->ignore($this->route('production')->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'unite_production' => ['required', Rule::in(['sujet', 'kg', 'oeuf'])],
        ];
    }
}