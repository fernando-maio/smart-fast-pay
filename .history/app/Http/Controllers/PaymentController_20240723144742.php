<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;
use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
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
