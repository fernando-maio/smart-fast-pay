<?php

namespace App\Exceptions;

use Exception;

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
     * Renderize a exceção para uma resposta HTTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['error' => $this->getMessage()], 500);
    }
}
