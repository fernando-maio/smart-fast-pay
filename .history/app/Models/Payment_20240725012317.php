<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
