<?php

namespace App\Http\Controllers;

use App\Models\DiscussionView;
use Illuminate\Http\Request;

class DiscussionViewController extends Controller
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

        $user->disicussionview()->create([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DiscussionView  $discussionView
     * @return \Illuminate\Http\Response
     */
    public function show(DiscussionView $discussionView)
    {

        $discussionView->view = $discussionView->view + 1;
        $discussionView->save();
        return $discussionView;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DiscussionView  $discussionView
     * @return \Illuminate\Http\Response
     */
    public function edit(DiscussionView $discussionView)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DiscussionView  $discussionView
     * @return \Illuminate\Http\Response
     */
    public function addview($id)
    {

        $data = DiscussionView::firstOrNew(['discussion_id' => $id]);

        $data->view = $data->view + 1;

        $data->save();
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DiscussionView  $discussionView
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiscussionView $discussionView)
    {
        //
    }
}
