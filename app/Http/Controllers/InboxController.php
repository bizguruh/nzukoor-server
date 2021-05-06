<?php

namespace App\Http\Controllers;

use App\Http\Resources\InboxResource;
use App\Models\Inbox;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInbox()
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $sender_type = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender_type = 'user';
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $sender_type = 'admin';
        }

        $inbox = Inbox::where('organization_id', $user->organization_id)->where([
            ['sender_id', '=', $user->id],
            ['sender_type', '=', $sender_type]

        ])->orWhere([
            ['receiver_id', '=', $user->id],
            ['receiver_type', '=', $sender_type]

        ])->get();

        return InboxResource::collection($inbox);
    }


    public function store(Request $request)
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $sender_type = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender_type = null;
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $sender_type = 'admin';
        }

        return Inbox::create([
            'message' => $request->message,
            'attachment' => $request->attachment,
            'sender_id' => $user->id,
            'sender_type' => $sender_type,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'organization_id' => $user->organization_id,
            'status' => 'sent',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inbox  $inbox
     * @return \Illuminate\Http\Response
     */
    public function markDelivered($id)
    {
        $inbox = Inbox::find($id);
        $inbox->status = 'delivered';
        $inbox->save();
        return $inbox;
    }


    public function update(Request $request, Inbox $inbox)
    {
        //
    }


    public function destroy(Inbox $inbox)
    {
        $inbox->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
