<?php

namespace Tests\Unit;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Tests\Helpers\PaymentDataHelper;
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
        $paymentService = new PaymentService();

        $result = $paymentService->processPayment();

        $this->assertEquals('paid', $result);
    }

    public function testProcessPaymentFailure()
    {
        $paymentService = new PaymentService();

        $result = $paymentService->processPayment();

        $this->assertEquals('failed', $result);
    }

    public function testCalculateFeePix()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'pix';

        $fee = $paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(1.5, $fee);
    }

    public function testCalculateFeeBoleto()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'boleto';

        $fee = $paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(2, $fee);
    }

    public function testCalculateFeeBankTransfer()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'bank_transfer';

        $fee = $paymentService->calculateFee($amount, $paymentMethodSlug);

        $this->assertEquals(4, $fee);
    }

    public function testCalculateFeeInvalidMethod()
    {
        $paymentService = new PaymentService();
        $amount = 100;
        $paymentMethodSlug = 'invalid_method';

        $this->expectException(InvalidPaymentMethodException::class);

        $paymentService->calculateFee($amount, $paymentMethodSlug);
    }
}