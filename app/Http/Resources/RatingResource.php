<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RatingResource extends Resource
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
        'user_image' => empty($this->user->photo) ? url('/') . '/assets/images/noimage.png' : '/assets/images/users/' . $this->user->photo,
        'user_id' => $this->user_id,
        'name' => $this->user->name,
        'review' => $this->review,
        'rating' => $this->rating,
        'review_date' => $this->review_date,
      ];
    }
}
