<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PaymentResource extends JsonResource
{
    /**
     * Transforming the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_client' => $this->name_client,
            'cpf' => $this->cpf,
            'description' => $this->description,
            'amount' => $this->amount,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_method_slug' => $this->payment_method_slug,
            'paid_at' => $this->paid_at ? Carbon::parse($this->paid_at)->toDateTimeString() : null,
        ];
    }
}
