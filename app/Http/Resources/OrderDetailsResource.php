<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class OrderDetailsResource extends Resource
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
        'shipping_name' => empty($this->shipping_name) ? $this->customer_name : $this->shipping_name,
        'shipping_email' => empty($this->shipping_email) ? $this->customer_email : $this->shipping_email,
        'shipping_phone' => empty($this->shipping_phone) ? $this->customer_phone : $this->shipping_phone,
        'shipping_address' => empty($this->shipping_address) ? $this->customer_address : $this->shipping_address,
        'shipping_zip' => empty($this->shipping_zip) ? $this->customer_zip : $this->shipping_zip,
        'shipping_city' => empty($this->shipping_city) ? $this->customer_city : $this->shipping_city,
        'shipping_country' => empty($this->shipping_country) ? $this->customer_country : $this->shipping_country,
        'customer_name' => $this->customer_name,
        'customer_email' => $this->customer_email,
        'customer_phone' => $this->customer_phone,
        'customer_address' => $this->customer_address,
        'customer_zip' => $this->customer_zip,
        'customer_city' => $this->customer_city,
        'customer_country' => $this->customer_country,
        'shipping' => $this->shipping,
        'paid_amount' => $this->currency_sign . '' . round($this->pay_amount * $this->currency_value , 2),
        'payment_method' => $this->method,
        'shipping_cost' => $this->shipping_cost,
        'packing_cost' => $this->packing_cost,
        'charge_id' => $this->charge_id,
        'transaction_id' => $this->txnid,
        'ordered_products' => $this->when(!empty($this->cart), function() {
          $cart = unserialize(bzdecompress(utf8_decode($this->cart)));
          return $cart->items;
        }),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
