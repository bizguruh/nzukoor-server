<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Inbox;
use App\Http\Resources\UserNameResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $id = auth('api')->user()->id;


        if ($this->user_id != $id) {
            $user =  new UserNameResource(User::find($this->user_id));
            $message = Inbox::where([['user_id', '=', $id], ['receiver_id', '=', $this->user_id]])
            ->orWhere([['receiver_id', '=', $id], ['user_id', '=', $this->following_id]])->get();
        } else {
            $user =  new UserNameResource(User::find($this->following_id));
            $message = Inbox::where([['user_id', '=', $id], ['receiver_id', '=', $this->following_id]])
            ->orWhere([['receiver_id', '=', $id], ['user_id', '=', $this->following_id]])->get();
        }
        $last_message = $message->last();
        $unread_messages = $message->filter(function ($a) use ($id) {
            return !$a['is_read'] && $a['receiver_id'] == $id;
        })->count();

        return [
            'id' => $this->id,
            'user_follower' =>  $user,
            'last_message' => $last_message,
            'unread_message' => $unread_messages,
            'last_message_time' => $last_message->created_at


        ];
    }
}
