<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\User;
use App\Models\FeedLike;
use Illuminate\Http\Request;
use App\Notifications\FeedInteraction;

class FeedLikeController extends Controller
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
    public function create()
    {
        //
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

        $data = $user->likes()->firstOrNew([
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'feed_id' => $request->id,
        ]);


        $data->like = !$data->like;
        $data->save();


        $title = $user->username . ' liked your post - Nzukoor';
        $detail = [
            'title' => $title,
            'message' => Feed::find($request->id)->message,
            'image' => Feed::find($request->id)->media,
            'url' => "https://nzukoor.com/member/feed/" . $request->id

        ];

        if ($user->id !== Feed::find($request->id)->user_id) {
            $creator = User::find(Feed::find($request->id)->user_id);
            $creator->notify(new FeedInteraction($detail));
        }
        return $data->load('admin', 'user', 'facilitator', 'feed');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedLike  $feedLike
     * @return \Illuminate\Http\Response
     */
    public function show(FeedLike $feedLike)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedLike  $feedLike
     * @return \Illuminate\Http\Response
     */
    public function edit(FeedLike $feedLike)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeedLike  $feedLike
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeedLike $feedLike)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedLike  $feedLike
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeedLike $feedLike)
    {
        //
    }
}
