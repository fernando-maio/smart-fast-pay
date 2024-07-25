<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case pix = 'pix';
    case boleto = 'boleto';
    case bank_transfer = 'bank_transfer';
}
