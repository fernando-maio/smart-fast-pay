<?php

namespace Tests\Helpers;

class AuthDataHelper
{
    public static function getPaymentData(array $overrides = []): array
    {
        $defaultData = [
            'name_client' => 'John Doe',
            'cpf' => '12345678901',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'pix',
        ];

        return array_merge($defaultData, $overrides);
    }
}
