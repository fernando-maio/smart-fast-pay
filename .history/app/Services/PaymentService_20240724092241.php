<?php

namespace App\Services;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentService implements PaymentServiceInterface
{
    public function processPayment()
    {
        // Payment processing simulation
        $successChance = 0.7;
        $isSuccessful = rand(0, 100) / 100 <= $successChance;

        return $isSuccessful ? 'paid' : 'failed';
    }

    public function createPayment(array $data): Payment
    {
        $merchant = User::findOrFail($data['merchant_id']);

        $fee = match($data['payment_method_slug']) {
            'pix' => 1.5,
            'boleto' => 2.0,
            'bank_transfer' => 4.0,
            default => throw new InvalidPaymentMethodException('Invalid payment method.'),
        };

        DB::beginTransaction();
        try {            
            $netAmount = round(($data['amount'] + $fee), 2);

            $payment = Payment::create([
                'name_client' => $data['name_client'],
                'cpf' => $data['cpf'],
                'description' => $data['description'],
                'amount' => $netAmount,
                'status' => 'pending',
                'payment_method_slug' => $data['payment_method_slug'],
                'merchant_id' => $data['merchant_id'],
            ]);

            $status = $this->processPayment();

            $payment->update([
                'status' => $status,
                'paid_at' => $status === 'paid' ? Carbon::now() : null,
            ]);

            if ($status === 'paid') {
                $merchant->balance += $netAmount;
                $merchant->save();
            }

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new PaymentProcessingException($e->getMessage());
        }
    }

    public function getAllPayments(int $merchantId): Collection
    {
        return Payment::where('merchant_id', $merchantId)->get();
    }

    public function getPaymentById(int $merchantId, $id): Payment
    {
        return Payment::where('merchant_id', $merchantId)->where('id', $id)->firstOrFail();
    }
}
