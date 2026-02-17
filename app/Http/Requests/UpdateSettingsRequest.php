<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hourly_rate' => 'required|numeric|min:0',
            'on_call_hourly_rate' => 'nullable|numeric|min:0',
            'extra_value' => 'required|numeric|min:0',
            'discount_value' => 'required|numeric|min:0',
            'auto_save_tracking' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'hourly_rate.required' => 'O valor por hora é obrigatório.',
            'hourly_rate.numeric' => 'O valor por hora deve ser um número.',
            'hourly_rate.min' => 'O valor por hora deve ser positivo.',
            'on_call_hourly_rate.numeric' => 'O valor de sobreaviso deve ser um número.',
            'on_call_hourly_rate.min' => 'O valor de sobreaviso deve ser positivo.',
            'extra_value.required' => 'O valor de acréscimo é obrigatório.',
            'extra_value.numeric' => 'O valor de acréscimo deve ser um número.',
            'extra_value.min' => 'O valor de acréscimo deve ser positivo.',
            'discount_value.required' => 'O valor de desconto é obrigatório.',
            'discount_value.numeric' => 'O valor de desconto deve ser um número.',
            'discount_value.min' => 'O valor de desconto deve ser positivo.',
        ];
    }
}
