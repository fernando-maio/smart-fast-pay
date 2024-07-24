<?php

namespace Tests\Unit;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test createPayment method.
     *
     * @return void
     */
    public function testCreatePayment()
    {
        // Mock the necessary dependencies
        $user = User::factory()->create();
        $data = [
            'name_client' => 'John Doe',
            'cpf' => '123456789',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'pix',
            'merchant_id' => $user->id,
        ];

        // Create an instance of the PaymentService
        $paymentService = new PaymentService();

        // Call the createPayment method
        $payment = $paymentService->createPayment($data);

        // Assert that the payment was created successfully
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals($data['amount'] + $paymentService->calculateFee($data['amount'], $data['payment_method_slug']), $payment->amount);
        $this->assertEquals($user->id, $payment->merchant_id);
    }

    /**
     * Test createPayment method with invalid payment method.
     *
     * @return void
     */
    public function testCreatePaymentWithInvalidPaymentMethod()
    {
        // Mock the necessary dependencies
        $user = User::factory()->create();
        $data = [
            'name_client' => 'John Doe',
            'cpf' => '123456789',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'invalid_method',
            'merchant_id' => $user->id,
        ];

        // Create an instance of the PaymentService
        $paymentService = new PaymentService();

        // Assert that an InvalidPaymentMethodException is thrown
        $this->expectException(InvalidPaymentMethodException::class);

        // Call the createPayment method
        $paymentService->createPayment($data);
    }

    /**
     * Test createPayment method with payment processing exception.
     *
     * @return void
     */
    public function testCreatePaymentWithPaymentProcessingException()
    {
        // Mock the necessary dependencies
        $user = User::factory()->create();
        $data = [
            'name_client' => 'John Doe',
            'cpf' => '123456789',
            'description' => 'Test payment',
            'amount' => 100.00,
            'payment_method_slug' => 'pix',
            'merchant_id' => $user->id,
        ];

        // Create an instance of the PaymentService
        $paymentService = new PaymentService();

        Mockery::mock($paymentService)->shouldReceive('processPayment')->andThrow(new \Exception('Payment processing failed'));
        $paymentService->shouldReceive('processPayment')->andThrow(new \Exception('Payment processing failed'));

        // Assert that a PaymentProcessingException is thrown
        $this->expectException(PaymentProcessingException::class);

        // Call the createPayment method
        $paymentService->createPayment($data);
    }

    /**
     * Test getAllPayments method.
     *
     * @return void
     */
    public function testGetAllPayments()
    {
        // Mock the necessary dependencies
        $user = User::factory()->create();
        $payment1 = Payment::factory()->create(['merchant_id' => $user->id]);
        $payment2 = Payment::factory()->create(['merchant_id' => $user->id]);

        // Create an instance of the PaymentService
        $paymentService = new PaymentService();

        // Call the getAllPayments method
        $payments = $paymentService->getAllPayments($user->id);

        // Assert that the correct payments are returned
        $this->assertCount(2, $payments);
        $this->assertTrue($payments->contains($payment1));
        $this->assertTrue($payments->contains($payment2));
    }

    /**
     * Test getPaymentById method.
     *
     * @return void
     */
    public function testGetPaymentById()
    {
        // Mock the necessary dependencies
        $user = User::factory()->create();
        $payment = Payment::factory()->create(['merchant_id' => $user->id]);

        // Create an instance of the PaymentService
        $paymentService = new PaymentService();

        // Call the getPaymentById method
        $foundPayment = $paymentService->getPaymentById($user->id, $payment->id);

        // Assert that the correct payment is returned
        $this->assertEquals($payment->id, $foundPayment->id);
    }
}
