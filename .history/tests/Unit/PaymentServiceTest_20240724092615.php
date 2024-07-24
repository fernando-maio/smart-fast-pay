<?php

namespace Tests\Unit;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function testCreatePayment()
    {
        $merchant = User::factory()->create();
        $data = [
            'name_client' => 'John Doe',
            'cpf' => '12345678901',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'pix',
            'merchant_id' => $merchant->id,
        ];

        $paymentService = new PaymentService();
        $payment = $paymentService->createPayment($data);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(round(($data['amount'] * 1.015), 2), $payment->amount);
        $this->assertEquals($data['merchant_id'], $payment->merchant_id);
    }

    public function testCreatePaymentWithInvalidMethod()
    {
        $merchant = User::factory()->create();
        $this->expectException(InvalidPaymentMethodException::class);

        $data = [
            'name_client' => 'John Doe',
            'cpf' => '123456789',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'invalid_method',
            'merchant_id' => $merchant->id,
        ];

        $paymentService = new PaymentService();
        $paymentService->createPayment($data);
    }

    public function testCreatePaymentWithException()
    {
        $merchant = User::factory()->create();
        $this->expectException(PaymentProcessingException::class);

        $data = [
            'name_client' => 'John Doe',
            'cpf' => '123456789',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'pix',
            'merchant_id' => $merchant->id,
        ];

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $paymentService = new PaymentService();
        $paymentService->createPayment($data);
    }

    public function testGetAllPayments()
    {
        $merchant = User::factory()->create();

        $payment1 = Payment::factory()->create([
            'merchant_id' => $merchantId,
        ]);

        $payment2 = Payment::factory()->create([
            'merchant_id' => $merchantId,
        ]);

        $paymentService = new PaymentService();
        $payments = $paymentService->getAllPayments($merchantId);

        $this->assertCount(2, $payments);
        $this->assertTrue($payments->contains($payment1));
        $this->assertTrue($payments->contains($payment2));
    }

    public function testGetPaymentById()
    {
        $merchantId = 1;
        $paymentId = 1;

        $payment = Payment::factory()->create([
            'merchant_id' => $merchantId,
            'id' => $paymentId,
        ]);

        $paymentService = new PaymentService();
        $foundPayment = $paymentService->getPaymentById($merchantId, $paymentId);

        $this->assertEquals($payment, $foundPayment);
    }
}
