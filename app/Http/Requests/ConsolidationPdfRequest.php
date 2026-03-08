<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidationPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'entry_ids'    => ['nullable', 'array'],
            'entry_ids.*'  => ['integer'],
            'on_call_ids'  => ['nullable', 'array'],
            'on_call_ids.*' => ['integer'],
        ];
    }
}
