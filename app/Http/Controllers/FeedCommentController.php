<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\User;
use App\Events\AddCommment;
use App\Models\FeedComment;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Models\FeedCommentReply;
use App\Notifications\LikeComment;
use App\Notifications\TaggedNotification;
use App\Http\Resources\FeedCommentResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\SingleFeedCommentResource;
use App\Http\Resources\FeedCommentRepliesResource;

class FeedCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function commentlike(Request $request)
    {
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $feed = FeedComment::find($request->feed_comment_id);
        $creator = User::find($feed->user_id);
        $mainfeed = Feed::find($feed->feed_id);
        // if ($mainfeed->user_id !== $user->id) {
        //     return response([
        //         'success' => false,
        //         'message' => 'only creator allowed'
        //     ], 401);
        // }

        $message = $user->username . ' liked your comment';
        $url = 'https://nzukoor.com/member/feed/view/' . $feed->feed_id;
        $details = [
            'message' => $message,
            'url' => $url
        ];


        $check = $user->feedcommentlikes()->where('feed_comment_id', $request->feed_comment_id)->first();
        if (is_null($check)) {
            $value =   $user->feedcommentlikes()->create([
                'feed_comment_id' => $request->feed_comment_id
            ]);
            if ($creator->id !== $user->id) {
                $creator->notify(new LikeComment($details));
                broadcast(new NotificationSent());
            }

            return response()->json('success');
        } else {
            $check->delete();
            return response()->json('removed');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $type = 'admin';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $type = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $type = 'user';
        }

        $data = $user->comments()->create([
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'feed_id' => $request->id,
            'comment' => $request->comment
        ]);

         broadcast(new AddCommment($user, new FeedCommentResource($data->load('user', 'feed'))))->toOthers();

        $regex = '(@\w+)';
        $tagged = [];
        if (preg_match_all($regex, $request->comment, $matches, PREG_PATTERN_ORDER)) {

            foreach ($matches[0] as $word) {
                $username = User::where('username', strtolower(str_replace('@', '', $word)))->first();
                if (!is_null($username)) {
                    array_push($tagged, $username);
                }
            }
            $details = [
                'body' => $user->username . ' mentioned you in a comment',
                'url' => 'https://nzukoor.com/member/feed/view/' . $request->id
            ];

            Notification::send($tagged, new TaggedNotification($details));
        }

        return  new SingleFeedCommentResource($data->load('user', 'feed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedComment  $feedComment
     * @return \Illuminate\Http\Response
     */
    public function feedcommentreplies($id)
    {
        return   FeedCommentRepliesResource::collection(FeedCommentReply::where('feed_comment_id', $id)->with('feedcommentreplylikes')->latest()->paginate(15));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedComment  $feedComment
     * @return \Illuminate\Http\Response
     */
    public function edit(FeedComment $feedComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeedComment  $feedComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeedComment $feedComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedComment  $feedComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeedComment $feedComment)
    {
        $feedComment->delete();
        return response('ok');
    }
}
