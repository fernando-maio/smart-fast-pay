<?php

namespace Tests\Feature;

use App\Enums\PaymentTypeEnum;
use App\Exceptions\InvalidPaymentMethodException;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentListServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;
    protected $configFees;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->app->make(PaymentService::class);
        $this->configFees = config('payment.fees');
    }

    public function testCalculateFeePix()
    {
        $amount = 100;
        $paymentMethodSlug = PaymentTypeEnum::Pix->value;

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug, $this->configFees);

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
