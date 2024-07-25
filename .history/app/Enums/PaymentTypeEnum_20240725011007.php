<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pix = 'pix';
    case boleto = 'boleto';
    case bank_transfer = 'bank_transfer';
}
