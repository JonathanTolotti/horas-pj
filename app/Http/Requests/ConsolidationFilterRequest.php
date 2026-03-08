<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidationFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'start_date'           => ['required', 'date'],
            'end_date'             => ['required', 'date', 'after_or_equal:start_date'],
            'filter_company_ids'   => ['nullable', 'array'],
            'filter_company_ids.*' => ['integer'],
            'filter_project_ids'   => ['nullable', 'array'],
            'filter_project_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required'          => 'A data de início é obrigatória.',
            'start_date.date'              => 'Data de início inválida.',
            'end_date.required'            => 'A data de fim é obrigatória.',
            'end_date.date'                => 'Data de fim inválida.',
            'end_date.after_or_equal'      => 'A data de fim deve ser igual ou posterior à data de início.',
        ];
    }
}
