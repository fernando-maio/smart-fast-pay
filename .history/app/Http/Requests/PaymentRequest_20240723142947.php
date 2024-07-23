<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_client' => 'required|string|max:255',
            'cpf' => 'required|max:14',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_method_slug' => 'required|string|exists:payment_methods,slug',
            'merchant_id' => 'required|exists:merchants,id',
        ];
    }

    public function messages()
    {
        return [
            'name_client.required' => 'O nome do cliente é obrigatório.',
            'name_client.string' => 'O nome do cliente deve ser uma string.',
            'name_client.max' => 'O nome do cliente não pode exceder 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.max' => 'O CPF não pode exceder 14 caracteres.',
            'description.string' => 'A descrição deve ser uma string.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'payment_method_slug.required' => 'O método de pagamento é obrigatório.',
            'payment_method_slug.string' => 'O método de pagamento deve ser uma string.',
            'payment_method_slug.exists' => 'O método de pagamento deve ser válido.',
            'merchant_id.required' => 'O ID do comerciante é obrigatório.',
            'merchant_id.exists' => 'O ID do comerciante deve ser válido.',
        ];
    }
}
