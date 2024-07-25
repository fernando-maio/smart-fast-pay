<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pending = 'pix';
    case Paid = 'boleto';
    case Expired = 'expired';
    case Failed = 'failed';
}
