<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoicePoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'xmls'   => ['required', 'array', 'min:1', 'max:50'],
            'xmls.*' => ['file', 'mimes:xml', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'xmls.required' => 'Selecione pelo menos um arquivo XML.',
            'xmls.array'    => 'Formato de envio inválido.',
            'xmls.max'      => 'Envie no máximo 50 arquivos por vez.',
            'xmls.*.file'   => 'Um dos arquivos enviados é inválido.',
            'xmls.*.mimes'  => 'Todos os arquivos devem ser XMLs válidos.',
            'xmls.*.max'    => 'Cada arquivo não pode ter mais de 2MB.',
        ];
    }
}
