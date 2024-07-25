<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case pix = 'pix';
    case boleto = 'boleto';
    case Expired = 'bank_transfer';
}
