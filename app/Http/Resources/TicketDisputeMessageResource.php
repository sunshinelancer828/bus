<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TicketDisputeMessageResource extends Resource
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
        'user_id' => $this->user_id,
        'conversation_id' => $this->conversation_id,
        'message' => $this->message,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
