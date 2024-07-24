<?php

namespace App\Services;

use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Interface\PaymentServiceInterface;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentService implements PaymentServiceInterface
{
    private function processPayment()
    {
        // Payment processing simulation
        $successChance = 0.7;
        $isSuccessful = rand(0, 100) / 100 <= $successChance;

        return $isSuccessful ? 'paid' : 'failed';
    }

    public function createPayment(array $data): Payment
    {
        $merchant = User::findOrFail($data['merchant_id']);

        DB::beginTransaction();
        try {
            $fee = $this->calculateFee($data['amount'], $data['payment_method_slug']);
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

    public function getAllPayments(int $userId): Collection
    {
        return Payment::all();
    }

    public function getPaymentById($id)
    {
        return Payment::findOrFail($id);
    }

    private function calculateFee($amount, $paymentMethodSlug)
    {
        $feePercentage = 0;

        switch ($paymentMethodSlug) {
            case 'pix':
                $feePercentage = 0.015;
                break;
            case 'boleto':
                $feePercentage = 0.02;
                break;
            case 'bank_transfer':
                $feePercentage = 0.04;
                break;
            default:
                throw new InvalidPaymentMethodException();
        }

        return $amount * $feePercentage;
    }
}
