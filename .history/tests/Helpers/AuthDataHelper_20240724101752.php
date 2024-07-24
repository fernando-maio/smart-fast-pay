<?php

namespace Tests\Helpers;

class AuthDataHelper
{
    public static function getPaymentData(array $overrides = []): array
    {
        $defaultData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        return array_merge($defaultData, $overrides);
    }
}
