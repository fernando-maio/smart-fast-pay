<?php

namespace App\Interface;

interface PaymentServiceInterface
{
    public function createPayment(array $data);
    public function getAllPayments();
    public function getPaymentById($id);
}
