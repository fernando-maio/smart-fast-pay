<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use App\Interface\PaymentServiceInterface;
use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentServiceInterface as ServicesPaymentServiceInterface;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(ServicesPaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $userId = Auth::id();
        $payments = $this->paymentService->getAllPayments($userId);
        return PaymentResource::collection($payments);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return new PaymentResource($payment);
    }

    public function store(PaymentRequest $request)
    {
        try {
            $payment = $this->paymentService->createPayment($request->validated());
            return response()->json($payment, 201);
        } catch (InvalidPaymentMethodException $e) {
            return $e->render($request);
        } catch (PaymentProcessingException $e) {
            return $e->render($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
