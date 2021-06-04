<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SubcategoryResource extends Resource
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
        'category_id' => $this->category_id,
        'name' => $this->name,
        'child_categories' => route('childcategories', $this->id),
        'attributes' => route('attibutes', $this->id) . '?type=subcategory',
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
