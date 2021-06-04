<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ServiceDetailsResource extends Resource
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
            'title' => $this->title,
            'details' => $this->details,
            'photo' => url('/') . '/assets/images/services/'.$this->photo,
        ];
    }
}
