<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ConversationResource extends Resource
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
            'subject' => $this->subject,
            'sent_user' => $this->sent_user,
            'recieved_user' => $this->recieved_user,
            'message' => $this->message,
            'messages' => ConversationMessageResource::collection($this->messages),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}
