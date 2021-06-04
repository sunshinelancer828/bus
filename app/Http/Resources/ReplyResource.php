<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ReplyResource extends Resource
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
        'comment' => $this->text,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
