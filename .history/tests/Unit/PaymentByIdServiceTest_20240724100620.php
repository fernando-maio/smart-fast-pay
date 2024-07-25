<?php

namespace Tests\Unit;

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
        $paymentService = new PaymentService();
        $result = $paymentService->processPayment();

        $this->assertContains($result, ['paid', 'failed']);
    }

    public function testCalculateFeeWithValidMethod()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'pix';
        $expectedFee = $amount * 0.015;

        $fee = $paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals($expectedFee, $fee);
    }

    public function testCalculateFeeWithInvalidMethod()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'invalid_method';

        $this->expectException(InvalidPaymentMethodException::class);

        $paymentService->calculateFee($amount, $paymentMethodSlug);
    }
}
