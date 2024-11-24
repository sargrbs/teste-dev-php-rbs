<?php
namespace App\Http\Requests;

use App\Rules\DocumentValidation;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'document_type' => 'required|in:CPF,CNPJ',
            'email' => 'required|email',
            'phone' => 'required|string',
            'street' => 'required|string',
            'number' => 'required|string',
            'neighborhood' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|size:8',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = str_replace('required|', '', $rule);
            }
        }

        $documentRule = ['required', 'string'];

        if ($this->isMethod('POST') || $this->has('document')) {
            $documentRule[] = 'unique:suppliers,document' .
                ($this->route('supplier') ? ',' . $this->route('supplier') : '');
        }

        $rules['document'] =  [
                'required',
                'string',
                new DocumentValidation($this->document_type),
                'unique:suppliers,document' . ($this->supplier ? ',' . $this->supplier->id : '')
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'document.required' => 'O documento é obrigatório',
            'document_type.required' => 'O tipo de documento é obrigatório',
            'document_type.in' => 'O tipo de documento deve ser CPF ou CNPJ',
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'O e-mail deve ser válido',
            'phone.required' => 'O telefone é obrigatório',
            'street.required' => 'A rua é obrigatória',
            'number.required' => 'O número é obrigatório',
            'neighborhood.required' => 'O bairro é obrigatório',
            'city.required' => 'A cidade é obrigatória',
            'state.required' => 'O estado é obrigatório',
            'state.size' => 'O estado deve conter 2 caracteres',
            'zip_code.required' => 'O CEP é obrigatório',
            'zip_code.size' => 'O CEP deve conter 8 dígitos',
        ];
    }
}
