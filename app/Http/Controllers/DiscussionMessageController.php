<?php

namespace App\Http\Controllers;

use App\Models\DiscussionMessage;
use Illuminate\Http\Request;

class DiscussionMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getdiscussionmessages($id)
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        } else {
            $user = auth('api')->user();
        }
        $message = DiscussionMessage::where('organization_id', $user->organization_id)->where('discussion_id', $user->id)->get();
    }


    public function store(Request $request)
    {



        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $facilitator_id = $user->id;
            $user_id = null;
        } else {
            $user = auth('api')->user();
            $user_id = $user->id;
            $facilitator_id = null;
        }

        return DiscussionMessage::create([

            'user_id' => $user_id,
            'facilitator_id' => $facilitator_id,
            'message' => $request->message,
            'attachment' => $request->attachment,
            'discussion_id' => $request->discussion_id,
            'organization_id' => $user->organization_id
        ]);
    }


    public function show(DiscussionMessage $discussionMessage)
    {
        return $discussionMessage;
    }


    public function update(Request $request, DiscussionMessage $discussionMessage)
    {
        //
    }

    public function destroy(DiscussionMessage $discussionMessage)
    {
        $discussionMessage->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
