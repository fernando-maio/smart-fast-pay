<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $fees = config('payment.fees');
        $paymentMethodSlug = $this->faker->randomElement(['pix', 'bank_transfer', 'boleto']);
        $appliedTax = $fees[$paymentMethodSlug];

        return [
            'name_client' => $this->faker->name,
            'cpf' => $this->faker->numerify('###########'),
            'description' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            '$appliedTax,
            'payment_method_slug' => $paymentMethodSlug,
        ];
    }
}
