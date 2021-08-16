<?php

namespace App\Http\Controllers;

use App\Models\FeedLike;
use Illuminate\Http\Request;

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
