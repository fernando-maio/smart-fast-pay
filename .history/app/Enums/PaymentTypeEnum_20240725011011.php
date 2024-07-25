<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pix = 'pix';
    case Boleto = 'boleto';
    case Bank_transfer = 'bank_transfer';
}
