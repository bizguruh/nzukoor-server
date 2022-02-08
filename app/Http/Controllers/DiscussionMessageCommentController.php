<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Discussion;
use Illuminate\Http\Request;
use App\Notifications\CommentReply;
use App\Models\DiscussionMessageComment;
use App\Notifications\NewDiscussionReply;
use App\Notifications\TaggedNotification;
use Illuminate\Support\Facades\Notification;

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
        if (!auth('api')->user() ) {
            return ('Unauthorized');
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

        $discussion = Discussion::find($request->discussion_id);
        $body = $user->username . ' replied your comment - ' . $discussion->name;
        $owner = User::find(DiscussionMessageComment::where('discussion_message_id', $request->message_id)->value('user_id'));
        $details = [
            'from_email' => 'info@nzukoor.com',
            'from_name' => 'Nzukoor',
            'greeting' => 'Hello ' . $owner->username,
            'body' => $body,
            'actionText' => 'Click to view',
            'url' => "https://nzukoor.com/me/discussion/" . $request->discussion_id,
            'id' => $request->discussion_id,
            'type' =>'discussion',
            'tribe_id'=> $discussion->tribe_id

        ];

        if ($owner->username !== $user->username) {
            $owner->notify(new CommentReply($details));
        }

        $regex = '(@\w+)';
        $tagged = [];
        if (preg_match_all($regex, $request->message, $matches, PREG_PATTERN_ORDER)) {

            foreach ($matches[0] as $word) {
                $username = User::where('username', strtolower(str_replace('@', '', $word)))->first();
                if (!is_null($username)) {
                    array_push($tagged, $username);
                }
            }
            $detail = [
                'body' => $user->username . ' mentioned you in a discussion reply',
                'url' => 'https://nzukoor.com/me/tribe/'.$discussion->tribe_id.'/discussion/'. $discussion->id,
                'type' => 'discussion',
                'id' => $discussion->id,
                'tribe_id' => $discussion->tribe_id,
                'message' => $request->message
            ];

            Notification::send($tagged, new TaggedNotification($detail));
        }
        return response($data->load( 'user'), 201);
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
