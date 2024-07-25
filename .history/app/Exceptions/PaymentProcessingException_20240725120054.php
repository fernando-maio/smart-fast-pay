<?php

namespace App\Exceptions;

use Exception;

/**
 * @OA\Schema(
 *     schema="PaymentProcessingException",
 *     description="Exception thrown when payment processing fails."
 * )
 */
class PaymentProcessingException extends Exception
{
    /**
     * Create a new instance of the exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'Payment processing failed.')
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
        return response()->json(['error' => $this->getMessage()], 500);
    }
}
