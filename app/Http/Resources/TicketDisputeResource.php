<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\TicketDisputeMessageResource;

class TicketDisputeResource extends Resource
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
        'subject' => $this->subject,
        'message' => $this->message,
        'type' => $this->type,
        'order_number' => $this->when($this->type == 'Dispute', $this->order_number),
        'messages' => TicketDisputeMessageResource::collection($this->messages),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
