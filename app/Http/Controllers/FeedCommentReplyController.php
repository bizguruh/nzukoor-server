<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Models\FeedCommentReply;
use App\Notifications\LikeComment;

class FeedCommentReplyController extends Controller
{
    public function store(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }


        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $data = $user->feedcommentreplies()->create([

            'message' => $request->message,
            'feed_id' => $request->feed_id,
            'feed_comment_id' => $request->feed_comment_id,

        ]);

        $feed = Feed::find($request->feed_id)->value('message');
        $body = $user->username . ' replied your comment - ' . $feed;
        $owner = User::find(FeedCommentReply::where('feed_comment_id', $request->feed_comment_id)->value('user_id'));
        $details = [
            'from_email' => 'nzukoor@gmail.com',
            'from_name' => 'Nzukoor',
            'greeting' => 'Hello ' . $owner->username,
            'body' => $body,
            'actionText' => 'Click to view',
            //  'url' => "https://nzukoor.com/explore/discussion/" . $request->discussion_id,

        ];

        // if (!$owner->username !== $user->username) {
        //     $owner->notify(new CommentReply($details));
        // }

        return response($data->load('user'), 201);
    }
    public function replylike(Request $request)
    {
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        $feed = FeedCommentReply::find($request->feed_comment_reply_id);
        $creator = User::find($feed->user_id);
        $message = $user->username . ' liked your reply';
        $url = 'https://nzukoor.com/member/feed/view/' . $feed->feed_id;
        $details = [
            'message' => $message,
            'url' => $url
        ];

        $check = $user->feedcommentreplylikes()->where('feed_comment_reply_id', $request->feed_comment_reply_id)->first();
        if (is_null($check)) {
            $value =   $user->feedcommentreplylikes()->create([
                'feed_comment_reply_id' => $request->feed_comment_reply_id
            ]);
            if ($creator->id !== $user->id) {
                $creator->notify(new LikeComment($details));
                broadcast(new NotificationSent());
            }
            return $value;
        } else {
            $check->delete();
            return response()->json('deleted');
        }
    }
}
