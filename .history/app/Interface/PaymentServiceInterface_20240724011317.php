<?php

namespace App\Inter;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentServiceInterface
{
    public function createPayment(array $data): Payment;
    public function getAllPayments(int $userId): Collection;
    public function getPaymentById(int $userId, $id): Payment;
}
