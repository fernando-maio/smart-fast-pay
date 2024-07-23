<?php

namespace App\Exceptions;

use Exception;

class PaymentProcessingException extends Exception
{
    /**
     * Crie uma nova instância da exceção.
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
