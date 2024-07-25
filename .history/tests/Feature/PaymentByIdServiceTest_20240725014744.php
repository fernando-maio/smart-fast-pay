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

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->app->make(PaymentService::class);
    }

    public function testProcessPayment()
    {
        $result = $this->paymentService->processPayment();

        $this->assertContains($result, ['paid', 'failed']);
    }

    public function testCalculateFeeWithValidMethod()
    {
        $configFees = config('payment.fees');
        $amount = 100;
        $paymentMethodSlug = 'pix';
        $expectedFee = $amount * $configFees['pix'];

        $fee = $this->paymentService->calculateFee($amount, $paymentMethodSlug, $configFees);

        $this->assertEquals($expectedFee, $fee);
    }

    public function testCalculateFeeWithInvalidMethod()
    {
        $amount = 100;
        $paymentMethodSlug = 'invalid_method';

        $this->expectException(InvalidPaymentMethodException::class);

        $this->paymentService->calculateFee($amount, $paymentMethodSlug);
    }
}
