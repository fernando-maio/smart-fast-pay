<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'name_client' => $this->faker->name,
            'cpf' => $this->faker->numerify(), // Adjust the mask as per your requirements
            'description' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Example: generates a random amount between 10 and 1000
            'payment_method_slug' => $this->faker->randomElement(['pix', 'credit_card', 'boleto']), // Example payment methods
            'merchant_id' => \App\Models\User::factory(), // Assuming the merchant is a User model
        ];
    }
}
