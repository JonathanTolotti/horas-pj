<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBillingGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name'               => ['required', 'string', 'max:150'],
            'company_ids'        => ['required', 'array', 'min:1'],
            'company_ids.*'      => ['integer', 'exists:companies,id,user_id,' . $userId],
            'primary_company_id' => ['nullable', 'integer', 'in_array:company_ids.*'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'O nome do grupo é obrigatório.',
            'name.max'                    => 'O nome do grupo pode ter no máximo 150 caracteres.',
            'company_ids.required'        => 'Selecione pelo menos uma empresa.',
            'company_ids.min'             => 'Selecione pelo menos uma empresa.',
            'company_ids.*.exists'        => 'Empresa inválida.',
            'primary_company_id.in_array' => 'A empresa principal deve fazer parte do grupo.',
        ];
    }
}
