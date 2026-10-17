<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevenueLimitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'annual_revenue_limit' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'annual_revenue_limit.numeric' => 'O limite deve ser um valor numérico.',
            'annual_revenue_limit.min'     => 'O limite não pode ser negativo.',
            'annual_revenue_limit.max'     => 'O limite informado é muito alto.',
        ];
    }
}
