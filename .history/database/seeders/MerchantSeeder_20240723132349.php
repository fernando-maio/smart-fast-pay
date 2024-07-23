<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
    public function run()
    {
        Merchant::create([
            'name' => 'Padrão Laravel',
            'balance' => 0.00,
        ]);
    }
}
