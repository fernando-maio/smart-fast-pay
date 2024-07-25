<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pending = 'pix';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';
}
