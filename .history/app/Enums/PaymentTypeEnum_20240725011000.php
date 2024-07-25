<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case pix = 'pix';
    case Paid = 'boleto';
    case Expired = 'bank_transfer';
}
