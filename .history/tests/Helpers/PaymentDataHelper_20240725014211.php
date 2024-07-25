<?php

namespace Tests\Helpers;

use App\Enums\PaymentTypeEnum;

class PaymentDataHelper
{
    public static function getPaymentData(array $overrides = []): array
    {
        $configFees = config('payment.fees');
        $defaultData = [
            'name_client' => 'John Doe',
            'cpf' => '12345678901',
            'description' => 'Test payment',
            'amount' => 100.00,
            'applied_tax' => config('payment.fees')
            'payment_method_slug' => PaymentTypeEnum::Pix->value,
        ];

        return array_merge($defaultData, $overrides);
    }
}
