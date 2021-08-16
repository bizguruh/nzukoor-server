<?php

namespace App\Http\Controllers;

use App\Models\DiscussionMessageComment;
use Illuminate\Http\Request;

class DiscussionMessageCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        return $message = DiscussionMessageComment::where('discussion_id', 3)->get();
    }

    public function getdiscussionmessages($id)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        $message = DiscussionMessageComment::where('discussion_id', $user->id)->get();
    }


    public function store(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }


        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $data = $user->discussionmessagecomment()->create([

            'message' => $request->message,
            'discussion_id' => $request->discussion_id,
            'discussion_message_id' => $request->message_id,
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
        ]);



        // broadcast(new AddDiscussion($user, $data->load('admin', 'user', 'facilitator')))->toOthers();
        return response($data->load('admin', 'user', 'facilitator'), 201);
    }


    public function show(DiscussionMessageComment $discussionMessageComment)
    {
        return $discussionMessageComment;
    }


    public function update(Request $request, DiscussionMessageComment $discussionMessageComment)
    {
        //
    }

    public function destroy(DiscussionMessageComment $discussionMessageComment)
    {
        $discussionMessageComment->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
