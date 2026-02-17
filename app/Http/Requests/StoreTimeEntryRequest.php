<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'Data inválida.',
            'start_time.required' => 'A hora de início é obrigatória.',
            'start_time.date_format' => 'Formato de hora inválido. Use HH:MM.',
            'end_time.required' => 'A hora de fim é obrigatória.',
            'end_time.date_format' => 'Formato de hora inválido. Use HH:MM.',
            'end_time.after' => 'A hora de fim deve ser posterior à hora de início.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres.',
            'project_id.exists' => 'Projeto não encontrado.',
        ];
    }
}
