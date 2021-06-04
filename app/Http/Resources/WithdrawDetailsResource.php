<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class WithdrawDetailsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'method' => $this->method,
            'acc_email' => $this->acc_email,
            'iban' => $this->iban,
            'country' => $this->country,
            'acc_name' => $this->acc_name,
            'address' => $this->address,
            'swift' => $this->swift,
            'reference' => $this->reference,
            'status' => ucfirst($this->status)
          ];
    }
}
