<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BlogResource extends Resource
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
            'id'                => $this->id,
            'title'             => $this->title,
            'details'           => strip_tags($this->details),
            'photo'             => url('/') . '/assets/images/blogs/'.$this->photo,
            'source'            => $this->source,
            'views'             => $this->views,
            'status'            => $this->status,
            'meta_tag'          => $this->meta_tag,
            'meta_description'  => $this->meta_description,
            'tags'              => $this->source,
            'created_at'        => $this->created_at
          ];
    }
}
