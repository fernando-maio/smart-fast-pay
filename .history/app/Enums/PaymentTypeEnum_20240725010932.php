<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';
}
