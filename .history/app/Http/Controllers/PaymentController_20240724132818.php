<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Http\Resources\PaymentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = $this->paymentService->getAllPayments(Auth::id());
        return response()->json(PaymentResource::collection($payments));
    }

    public function show($id)
    {
        try {
            $payment = $this->paymentService->getPaymentById(Auth::id(), $id);
            return new PaymentResource($payment);
        } catch (ModelNotFoundException $e) {
            return response()->json(response()->json(['error' => 'Payment not found.'], 404);
        }
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
