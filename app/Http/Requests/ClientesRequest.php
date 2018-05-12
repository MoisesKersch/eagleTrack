<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clnome' => 'required',
            'cldocumento' => 'required|cpfcnpj',
            'inputCidade' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'clnome.required'=>'O campo nome é obrigatório',
            'cldocumento.required' => 'É obrigatório informar esse documento',
            'inputCidade.required' => 'O campo cidade-UF é obrigatório',
            'cldocumento.cpfcnpj' => 'Este documento não é valido'
        ];
    }
}
