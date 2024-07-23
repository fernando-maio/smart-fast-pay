<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            ['name' => 'Pix', 'slug' => 'pix'],
            ['name' => 'Boleto', 'slug' => 'boleto'],
            ['name' => 'Bank Transfer', 'slug' => 'bank_transfer'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
