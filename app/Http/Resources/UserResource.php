<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Generalsetting;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $gs = Generalsetting::first();

      return [
        'id' => $this->id,
        'full_name' => $this->name,
        'phone' => $this->phone,
        'email' => $this->email,
        'fax' => $this->fax,
        'propic' => $this->photo ? url('/') . '/assets/images/users/' . $this->photo : url('/') . '/assets/images/'.$gs->user_image,
        'zip_code' => $this->zip,
        'city' => $this->city,
        'country' => $this->country,
        'address' => $this->address,
        'balance' => $this->current_balance,
        'email_verified' => $this->email_verified,
        'affilate_code' => $this->affilate_code,
        'affilate_income' => $this->affilate_income,
        'ban' => $this->ban,
      ];
    }
}
