<?php

namespace App\Enums;

enum PaymentTypeEnum
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';
}
