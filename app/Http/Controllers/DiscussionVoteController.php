<?php

namespace App\Http\Controllers;

use App\Models\DiscussionVote;
use Illuminate\Http\Request;

class DiscussionVoteController extends Controller
{


    public function store(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $sender = 'admin';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }


        $data = $user->discussionvote()->firstOrNew([
            'discussion_id' => intval($request->id),
        ]);
        $data->vote = $request->vote;
        $data->save();
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DiscussionVote  $discussionVote
     * @return \Illuminate\Http\Response
     */
    public function show(DiscussionVote $discussionVote, $id)
    {
    }
}
