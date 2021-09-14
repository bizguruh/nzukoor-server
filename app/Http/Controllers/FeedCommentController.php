<?php

namespace App\Http\Controllers;

use App\Events\AddCommment;
use App\Models\FeedComment;
use Illuminate\Http\Request;

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
        $check = $user->feedcommentlikes()->where('feed_comment_id', $request->feed_comment_id)->first();
        if (is_null($check)) {
            return   $user->feedcommentlikes()->create([
                'feed_comment_id' => $request->feed_comment_id
            ]);
        } else {
            $check->delete();
            return response()->json('deleted');
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

        broadcast(new AddCommment($user, $data->load('admin', 'user', 'facilitator', 'feed')))->toOthers();
        return $data->load('admin', 'user', 'facilitator', 'feed');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedComment  $feedComment
     * @return \Illuminate\Http\Response
     */
    public function show(FeedComment $feedComment)
    {
        //
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
        //
    }
}
