<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;

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
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Merchant not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
