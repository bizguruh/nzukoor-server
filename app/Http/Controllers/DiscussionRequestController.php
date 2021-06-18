<?php

namespace App\Http\Controllers;

use App\Models\DiscussionRequest;
use Illuminate\Http\Request;

class DiscussionRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        return $user->discussionrequest()->get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DiscussionRequest  $discussionRequest
     * @return \Illuminate\Http\Response
     */
    public function show(DiscussionRequest $discussionRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DiscussionRequest  $discussionRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(DiscussionRequest $discussionRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DiscussionRequest  $discussionRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DiscussionRequest $discussionRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DiscussionRequest  $discussionRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiscussionRequest $discussionRequest)
    {
        //
    }
}
