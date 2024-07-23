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
}
