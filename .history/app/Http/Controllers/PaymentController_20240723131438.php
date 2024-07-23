<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Merchant;
use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

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
        $merchant = Merchant::findOrFail($request->merchant_id);

        DB::beginTransaction();
        try {
            $fee = $this->paymentService->calculateFee($request->amount, $request->payment_method_slug);
            $netAmount = $request->amount - $fee;

            $payment = Payment::create([
                'name_client' => $request->name_client,
                'cpf' => $request->cpf,
                'description' => $request->description,
                'amount' => $netAmount,
                'status' => 'pending',
                'payment_method_slug' => $request->payment_method_slug,
            ]);

            $status = $this->paymentService->processPayment($payment);

            $payment->update(['status' => $status, 'paid_at' => $status === 'paid' ? now() : null]);

            if ($status === 'paid') {
                $merchant->balance += $netAmount;
                $merchant->save();
            }

            DB::commit();

            return response()->json($payment, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }
}
