<?php

namespace App\Exceptions;

use Exception;

/**
 * @OA\Schema(
 *     schema="InvalidPaymentMethodException",
 *     description="Exceção lançada quando um método de pagamento inválido é fornecido."
 * )
 */
class InvalidPaymentMethodException extends Exception
{
    /**
     * Create a new instance of the exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'Invalid payment method.')
    {
        parent::__construct($message);
    }

    /**
     * Render the exception to an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['error' => $this->getMessage()], 400);
    }
}
