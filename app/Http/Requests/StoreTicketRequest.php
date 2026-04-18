<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:150',
            'category' => ['required', new Enum(TicketCategory::class)],
            'body'     => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'O título é obrigatório.',
            'title.max'         => 'O título deve ter no máximo 150 caracteres.',
            'category.required' => 'A categoria é obrigatória.',
            'body.required'     => 'A descrição é obrigatória.',
            'body.max'          => 'A descrição deve ter no máximo 5.000 caracteres.',
        ];
    }
}
