<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class OrderResource extends Resource
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
        'number' => $this->order_number,
        'total' => $this->currency_sign . "" . round($this->pay_amount * $this->currency_value , 2),
        'status' => $this->status,
        'details' => route('order', $this->id),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
