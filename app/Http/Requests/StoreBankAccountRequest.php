<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name'      => ['required', 'string', 'max:100'],
            'branch'         => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'max:30'],
            'account_type'   => ['required', 'in:corrente,poupança'],
            'holder_name'    => ['required', 'string', 'max:150'],
            'pix_key'        => ['nullable', 'string', 'max:150'],
            'active'         => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required'      => 'O nome do banco é obrigatório.',
            'bank_name.max'           => 'O nome do banco não pode ter mais de 100 caracteres.',
            'branch.required'         => 'A agência é obrigatória.',
            'branch.max'              => 'A agência não pode ter mais de 20 caracteres.',
            'account_number.required' => 'O número da conta é obrigatório.',
            'account_number.max'      => 'O número da conta não pode ter mais de 30 caracteres.',
            'account_type.required'   => 'O tipo de conta é obrigatório.',
            'account_type.in'         => 'O tipo de conta deve ser corrente ou poupança.',
            'holder_name.required'    => 'O nome do titular é obrigatório.',
            'holder_name.max'         => 'O nome do titular não pode ter mais de 150 caracteres.',
            'pix_key.max'             => 'A chave PIX não pode ter mais de 150 caracteres.',
        ];
    }
}
