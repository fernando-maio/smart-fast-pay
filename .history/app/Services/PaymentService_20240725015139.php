<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
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
    public function processPayment(): string
    {
        // Payment processing simulation
        $successChance = 0.7;
        $isSuccessful = rand(0, 100) / 100 <= $successChance;

        return $isSuccessful ? PaymentStatusEnum::Paid->value : PaymentStatusEnum::Failed->value;
    }

    public function createPayment(array $data): Payment
    {
        $configFees = config('payment.fees');
        $merchant = User::findOrFail($data['merchant_id']);
        $fee = $this->calculateFee($data['amount'], $data['payment_method_slug'], $configFees);

        DB::beginTransaction();
        try {
            $netAmount = round(($data['amount'] + $fee), 2);

            $payment = Payment::create([
                'name_client' => $data['name_client'],
                'cpf' => $data['cpf'],
                'description' => $data['description'],
                'amount' => $netAmount,
                'applied_tax' => $configFees[$data['payment_method_slug']],
                'status' => PaymentStatusEnum::Pending->value,
                'payment_method_slug' => $data['payment_method_slug'],
                'merchant_id' => $data['merchant_id'],
            ]);

            $status = $this->processPayment();

            $payment->update([
                'status' => $status,
                'paid_at' => $status === PaymentStatusEnum::Paid->value ? Carbon::now() : null,
            ]);

            if ($status === PaymentStatusEnum::Paid->value) {
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

    public function calculateFee($amount, $paymentMethodSlug, $configFees): float
    {
        if (!isset($configFees[$paymentMethodSlug])) {
            throw new InvalidPaymentMethodException();
        }

        return $amount * $configFees[$paymentMethodSlug];
    }
}
