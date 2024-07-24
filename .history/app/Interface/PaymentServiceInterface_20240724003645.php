<?php

namespace App\Interface;

interface PaymentServiceInterface
{
    public function createPayment(array $data);
}
