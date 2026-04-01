<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'               => ['required', 'in:credit,debit'],
            'description'        => ['required', 'string', 'max:500'],
            'amount'             => ['required', 'numeric', 'min:0.01'],
            'date'               => ['required', 'date'],
            'reconcile_with_xml' => ['nullable', 'boolean'],
            'sort_order'         => ['nullable', 'integer', 'min:0'],
            'time_entry_id'      => ['nullable', 'integer', 'exists:time_entries,id'],
            'time_entry_ids'     => ['nullable', 'array'],
            'time_entry_ids.*'   => ['integer', 'exists:time_entries,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'        => 'O tipo do lançamento é obrigatório.',
            'type.in'              => 'O tipo deve ser crédito ou débito.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max'      => 'A descrição não pode ter mais de 500 caracteres.',
            'amount.required'      => 'O valor é obrigatório.',
            'amount.numeric'       => 'O valor deve ser numérico.',
            'amount.min'           => 'O valor deve ser maior que zero.',
            'date.required'        => 'A data é obrigatória.',
            'date.date'            => 'A data deve ser uma data válida.',
        ];
    }
}
