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

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->app->make(PaymentService::class);
    }

    public function testCreatePayment()
    {
        $merchant = User::factory()->create();
        $data = PaymentDataHelper::getPaymentData(['merchant_id' => $merchant->id]);

        $payment = $this->paymentService->createPayment($data);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(round(($data['amount'] * 1.015), 2), $payment->amount);
        $this->assertEquals($data['merchant_id'], $payment->merchant_id);
    }

    public function testCreatePaymentWithInvalidMethod()
    {
        $merchant = User::factory()->create();
        $this->expectException(InvalidPaymentMethodException::class);

        $data = PaymentDataHelper::getPaymentData([
            'merchant_id' => $merchant->id,
            'payment_method_slug' => 'invalid_method'
        ]);

        $this->paymentService->createPayment($data);
    }

    public function testCreatePaymentWithException()
    {
        $merchant = User::factory()->create();
        $this->expectException(PaymentProcessingException::class);

        $data = PaymentDataHelper::getPaymentData(['merchant_id' => $merchant->id]);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $this->paymentService->createPayment($data);
    }

    public function testGetAllPayments()
    {
        $merchant = User::factory()->create();

        $payment1 = Payment::factory()->create([
            'merchant_id' => $merchant->id,
        ]);

        $payment2 = Payment::factory()->create([
            'merchant_id' => $merchant->id,
        ]);

        $payments = $this->paymentService->getAllPayments($merchant->id);

        $this->assertCount(2, $payments);
        $this->assertTrue($payments->contains($payment1));
        $this->assertTrue($payments->contains($payment2));
    }

    public function testGetPaymentById()
    {
        $merchant = User::factory()->create();
        $payment = Payment::factory()->create([
            'merchant_id' => $merchant->id,
        ]);

        $foundPayment = $this->paymentService->getPaymentById($merchant->id, $payment->id);

        $this->assertEquals($payment->id, $foundPayment->id);
    }
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