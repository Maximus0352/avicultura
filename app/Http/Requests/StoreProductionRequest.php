<?php

namespace App\Http\Requests;

use App\Models\Production;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Production::class);
    }

    public function rules(): array
    {
        return [
            'nom' => [
                'required', 'string', 'max:255',
                Rule::unique('productions', 'nom')->where('exploitation_id', $this->user()->exploitation_id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'unite_production' => ['required', Rule::in(['sujet', 'kg', 'oeuf'])],
        ];
    }
}