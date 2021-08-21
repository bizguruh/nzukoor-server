<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Discussion;
use Illuminate\Http\Request;
use App\Events\AddDiscussion;
use App\Models\DiscussionMessage;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewDiscussionReply;

class DiscussionMessageController extends Controller
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
        return $message = DiscussionMessage::where('discussion_id', 3)->get();
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
        $message = DiscussionMessage::where('discussion_id', $user->id)->get();
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

        return  DB::transaction(function () use ($request, $user) {
            $data = $user->discussionmessage()->create([

                'message' => $request->message,
                'attachment' => $request->attachment,
                'publicId' => $request->publicId,
                'discussion_id' => $request->discussion_id,
                'organization_id' => $user->organization_id ? $user->organization_id : 1,
            ]);
            $type = 'discussion';
            $contribution =   $user->contribution()->firstOrNew();

            $contribution->type = $type;
            $contribution->discussion_id = $request->discussion_id;
            $contribution->count = $contribution->count + 1;
            $contribution->save();


            broadcast(new AddDiscussion($user, $data->load('admin', 'user', 'facilitator', 'discussionmessagecomment')))->toOthers();

            $title = $user->username . ' replied your discussion - ' . Discussion::find($request->discussion_id)->name;
            $detail = [
                'title' => $title,
                'message' => strip_tags($request->message),
                'url' => "/member/discussion/" . $request->discussion_id

            ];

            $creator = User::find(Discussion::find($request->discussion_id)->user_id);
            $creator->notify(new NewDiscussionReply($detail));

            return $data->load('admin', 'user', 'facilitator', 'discussionmessagecomment');
        });
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
