<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case Pix = 'pix';
    case Boleto = 'boleto';
    case BankTransfer = 'bank_transfer';
}
