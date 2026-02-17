<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'is_default' => 'boolean',
            'default_description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do projeto é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'default_description.max' => 'A descrição padrão deve ter no máximo 255 caracteres.',
        ];
    }
}
