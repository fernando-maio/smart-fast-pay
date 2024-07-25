<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     required={"name_client", "cpf", "description", "amount", "status", "payment_method_slug"},
 *     @OA\Property(property="id", type="integer", readOnly="true"),
 *     @OA\Property(property="name_client", type="string"),
 *     @OA\Property(property="cpf", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="amount", type="number"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="payment_method_slug", type="string"),
 *     @OA\Property(property="paid_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name_client',
        'cpf',
        'description',
        'amount',
        'applied_tax',
        'status',
        'payment_method_slug',
        'paid_at',
        'merchant_id'
    ];
}
