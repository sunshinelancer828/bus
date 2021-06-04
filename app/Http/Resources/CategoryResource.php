<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CategoryResource extends Resource
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
        'icon' => url('/') . '/assets/images/categories/'.$this->photo,
        'image' => $this->when($this->image, url('/') . '/assets/images/categories/'.$this->image),
        'count' => $this->products()->where('status', 1)->count() . ' item(s)',
        'subcategories' => route('subcategories', $this->id),
        'attributes' => route('attibutes', $this->id) . '?type=category',
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
