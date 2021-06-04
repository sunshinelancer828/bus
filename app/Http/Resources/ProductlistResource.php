<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductlistResource extends Resource
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
        'title' => $this->name,
        'thumbnail' => url('/') . '/assets/images/thumbnails/'.$this->thumbnail,
        'rating' => $this->ratings()->avg('rating') > 0 ? round($this->ratings()->avg('rating'), 2) : round(0.00, 2),
        'current_price' => $this->price,
        'previous_price' => $this->previous_price,
        'sale_end_date' => $this->when($this->is_discount == 1, $this->discount_date),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'product_code' => $this->product_code,
      ];
    }
}
