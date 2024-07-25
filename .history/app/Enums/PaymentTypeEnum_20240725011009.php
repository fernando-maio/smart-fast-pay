<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pix = 'pix';
    case Boleto = 'boleto';
    case bank_transfer = 'bank_transfer';
}
