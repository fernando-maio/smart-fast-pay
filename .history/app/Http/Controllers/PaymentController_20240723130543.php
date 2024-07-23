<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Merchant;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $request->validate([
            'name_client' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_method_slug' => 'required|exists:payment_methods,slug',
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $merchant = Merchant::findOrFail($request->merchant_id);

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'name_client' => $request->name_client,
                'cpf' => $request->cpf,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 'pending',
                'payment_method_slug' => $request->payment_method_slug,
            ]);

            $status = $this->paymentService->processPayment($payment);

            $payment->update(['status' => $status, 'paid_at' => $status === 'paid' ? now() : null]);

            if ($status === 'paid') {
                $merchant->balance += $payment->amount;
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

