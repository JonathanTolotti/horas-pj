<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'project_id' => 'nullable|exists:projects,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'start_datetime.required' => 'A data/hora de início é obrigatória.',
            'start_datetime.date' => 'Data/hora de início inválida.',
            'end_datetime.required' => 'A data/hora de fim é obrigatória.',
            'end_datetime.date' => 'Data/hora de fim inválida.',
            'end_datetime.after' => 'A data/hora de fim deve ser posterior à de início.',
            'project_id.exists' => 'Projeto não encontrado.',
            'hourly_rate.numeric' => 'O valor por hora deve ser um número.',
            'hourly_rate.min' => 'O valor por hora deve ser positivo.',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres.',
        ];
    }
}
