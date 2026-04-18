<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body'        => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'A mensagem é obrigatória.',
            'body.max'      => 'A mensagem deve ter no máximo 5.000 caracteres.',
        ];
    }
}
