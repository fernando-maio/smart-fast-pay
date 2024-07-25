<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="PaymentRequest",
 *     required={"name_client", "cpf", "description", "amount", "payment_method_slug", "merchant_id"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
 *    @OA\Property(property="balance", type="number", example="100.00"),
 * )
 */
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
            'merchant_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name_client.required' => 'Customer name is required.',
            'name_client.string' => 'The customer name must be a string.',
            'name_client.max' => 'Customer name cannot exceed 255 characters.',
            'cpf.required' => 'CPF is mandatory.',
            'cpf.max' => 'The CPF cannot exceed 14 characters.',
            'description.string' => 'The description must be a string.',
            'amount.required' => 'The amount is mandatory.',
            'amount.numeric' => 'The amount must be numeric.',
            'payment_method_slug.required' => 'Payment method is required.',
            'payment_method_slug.string' => 'Payment method must be a string.',
            'payment_method_slug.exists' => 'The payment method must be valid.',
            'merchant_id.required' => 'Merchant ID is required.',
            'merchant_id.exists' => 'Merchant ID must be valid.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $errors
        ], 422));
    }
}
