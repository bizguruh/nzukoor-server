<?php

namespace App\Http\Controllers;

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

        $data = $user->comments()->create([
            'organization_id' => $user->organization_id,
            'feed_id' => $request->id,
            'comment' => $request->comment
        ]);
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
