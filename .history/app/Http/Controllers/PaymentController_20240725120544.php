<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Exceptions\InvalidPaymentMethodException;
use App\Exceptions\PaymentProcessingException;
use App\Http\Resources\PaymentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0.0"
 * )
 */
class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Get all payments",
     *     tags={"Payments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of payments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $payments = $this->paymentService->getAllPayments(Auth::id());
        return response()->json(PaymentResource::collection($payments));
    }

    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     summary="Get payment by ID",
     *     tags={"Payments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Payment ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $payment = $this->paymentService->getPaymentById(Auth::id(), $id);
            return response()->json(new PaymentResource($payment));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Payment not found.'], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     )
     * )
     */
    public function store(PaymentRequest $request)
    {
        try {
            $payment = $this->paymentService->createPayment($request->validated());
            return response()->json(new PaymentResource($payment));
        } catch (InvalidPaymentMethodException $e) {
            return $e->render($request);
        } catch (PaymentProcessingException $e) {
            return $e->render($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
