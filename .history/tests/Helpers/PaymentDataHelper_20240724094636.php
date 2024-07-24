<?php

namespace Tests\Helpers;

class PaymentDataHelper
{
    public static function getPaymentData(array $overrides = []): array
    {
        $defaultData = [
            'user_id' => 1,
            'name_client' => 'John Doe',
            'cpf' => '123.456.789-00',
            'description' => 'Payment description',
            'amount' => 200,
            'payment_method_slug' => 'pix',
        ];

        return array_merge($defaultData, $overrides);
    }
}
