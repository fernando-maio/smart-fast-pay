<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function processPayment(Payment $payment)
    {
        // Simulação de processamento de pagamento
        $successChance = 0.7;
        $isSuccessful = rand(0, 100) / 100 <= $successChance;

        return $isSuccessful ? 'paid' : 'failed';
    }

    public function calculateFee($amount, $paymentMethodSlug)
    {
        $feePercentage = 0;

        switch ($paymentMethodSlug) {
            case 'pix':
                $feePercentage = 0.015;
                break;
            case 'boleto':
                $feePercentage = 0.02;
                break;
            case 'bank_transfer':
                $feePercentage = 0.04;
                break;
            default:
                throw new \Exception('Método de pagamento inválido.');
        }

        return $amount * $feePercentage;
    }
}
