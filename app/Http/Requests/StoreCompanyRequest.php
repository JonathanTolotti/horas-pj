<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'cnpj'                  => ['required', 'string', 'size:18', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/'],
            'active'                => 'boolean',
            'razao_social'          => 'nullable|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'telefone'              => 'nullable|string|max:20',
            'cep'                   => 'nullable|string|max:9',
            'logradouro'            => 'nullable|string|max:255',
            'numero'                => 'nullable|string|max:20',
            'complemento'           => 'nullable|string|max:255',
            'bairro'                => 'nullable|string|max:255',
            'cidade'                => 'nullable|string|max:255',
            'uf'                    => 'nullable|string|size:2',
            'inscricao_municipal'   => 'nullable|string|max:50',
            'inscricao_estadual'    => 'nullable|string|max:50',
            'responsavel_nome'      => 'nullable|string|max:255',
            'responsavel_email'     => 'nullable|email|max:255',
            'responsavel_telefone'  => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'O nome da empresa é obrigatório.',
            'name.max'                  => 'O nome deve ter no máximo 255 caracteres.',
            'cnpj.required'             => 'O CNPJ é obrigatório.',
            'cnpj.size'                 => 'O CNPJ deve ter 18 caracteres (com formatação).',
            'cnpj.regex'                => 'O CNPJ deve estar no formato 00.000.000/0000-00.',
            'email.email'               => 'O e-mail da empresa deve ser válido.',
            'responsavel_email.email'   => 'O e-mail do responsável deve ser válido.',
            'uf.size'                   => 'A UF deve ter exatamente 2 caracteres.',
        ];
    }
}
