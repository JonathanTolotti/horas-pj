<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'title'           => ['required', 'string', 'max:200'],
            'reference_month' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'company_id'      => ['nullable', 'exists:companies,id,user_id,' . $userId],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id,user_id,' . $userId],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'           => 'O título é obrigatório.',
            'title.max'                => 'O título não pode ter mais de 200 caracteres.',
            'reference_month.required' => 'A competência é obrigatória.',
            'reference_month.regex'    => 'A competência deve estar no formato AAAA-MM.',
            'company_id.exists'        => 'Empresa inválida.',
            'bank_account_id.exists'   => 'Conta bancária inválida.',
            'notes.max'                => 'As observações não podem ter mais de 2000 caracteres.',
        ];
    }
}
