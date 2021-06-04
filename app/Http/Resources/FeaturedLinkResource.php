<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FeaturedLinkResource extends Resource
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
            'name' => $this->name,
            'link' => $this->link,
            'photo' => url('/') . '/assets/images/featuredlink/'.$this->photo,
          ];
    }
}
