<?php

namespace Tests\Feature;

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

        $this->assertContains($result, ['paid', 'failed']);
    }

    public function testCalculateFeeWithValidMethod()
    {

        $amount = 100;
        $paymentMethodSlug = 'pix';
        $expectedFee = $amount * $this->configFees['pix'];

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
