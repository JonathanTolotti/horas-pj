<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'type'       => 'required|in:persistent,one_time',
            'color'      => 'required|in:blue,yellow,red,green',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'           => 'O título é obrigatório.',
            'title.max'                => 'O título não pode ter mais de 255 caracteres.',
            'message.required'         => 'A mensagem é obrigatória.',
            'type.required'            => 'O tipo é obrigatório.',
            'type.in'                  => 'O tipo deve ser "persistente" ou "uma vez".',
            'color.required'           => 'A cor é obrigatória.',
            'color.in'                 => 'A cor deve ser azul, amarelo, vermelho ou verde.',
            'start_date.required'      => 'A data de início é obrigatória.',
            'start_date.date'          => 'A data de início é inválida.',
            'end_date.date'            => 'A data de encerramento é inválida.',
            'end_date.after_or_equal'  => 'A data de encerramento deve ser igual ou posterior à data de início.',
        ];
    }
}
