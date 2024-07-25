<?php

namespace Tests\Unit;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentListServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->app->make(PaymentService::class);
    }

    
    public function testProcessPaymentSuccess()
    {
        $result = $this->paymentService->processPayment();

        $this->assertEquals('paid', $result);
    }

    public function testProcessPaymentFailure()
    {
        $result = $this->paymentService->processPayment();

        $this->assertEquals('failed', $result);
    }

    public function testCalculateFeePix()
    {
        $amount = 100;
        $paymentMethodSlug = 'pix';

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(1.5, $fee);
    }

    public function testCalculateFeeBoleto()
    {
        $amount = 100;
        $paymentMethodSlug = 'boleto';

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(2, $fee);
    }

    public function testCalculateFeeBankTransfer()
    {
        $amount = 100;
        $paymentMethodSlug = 'bank_transfer';

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(4, $fee);
    }

    public function testCalculateFeeInvalidMethod()
    {
        $amount = 100;
        $paymentMethodSlug = 'invalid_method';

        $this->expectException(InvalidPaymentMethodException::class);

        $this->paymentService->calculateFee($amount, $paymentMethodSlug);
    }
}
