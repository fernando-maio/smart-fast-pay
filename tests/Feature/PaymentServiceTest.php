<?php

namespace Tests\Feature;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Helpers\PaymentDataHelper;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->app->make(PaymentServiceInterface::class);
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
