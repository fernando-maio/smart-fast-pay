<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pending = 'pix';
    case Paid = 'boleto';
    case Expired = 'bank_transfer';
    case Failed = 'failed';
}
