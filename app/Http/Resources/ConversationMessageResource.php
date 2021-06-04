<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ConversationMessageResource extends Resource
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
            'conversation_id' => $this->conversation_id,
            'sent_user' => $this->sent_user,
            'recieved_user' => $this->recieved_user,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}
