<?php

namespace Tests\Feature;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Exceptions\InvalidPaymentMethodException;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentByIdServiceTest extends TestCase
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

    public function testProcessPayment()
    {
        $result = $this->paymentService->processPayment();

        $this->assertContains($result, [
            PaymentStatusEnum::Paid->value, 
            PaymentStatusEnum::Failed->value
        ]);
    }

    public function testCalculateFeeWithValidMethod()
    {
        $amount = 100;
        $paymentMethodSlug = PaymentTypeEnum::Pix->value;
        $expectedFee = $amount * $this->configFees[PaymentTypeEnum::Pix->value];

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug, $this->configFees);

        $this->assertEquals($expectedFee, $fee);
    }

    public function testCalculateFeeWithInvalidMethod()
    {
        $amount = 100;
        $paymentMethodSlug = 'invalid_method';

        $this->expectException(InvalidPaymentMethodException::class);

        $this->paymentService->calculateFee($amount, $paymentMethodSlug, $this->configFees);
    }
}
