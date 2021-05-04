<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        } else {
            $user = auth('api')->user();
        }
        return Discussion::where('organization_id', $user->organization_id)->with('discussionmessage')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $sender = 'facilitator';
        } else {
            $user = auth('api')->user();
            $sender = 'user';
        }
        return Discussion::create([
            'type' => $request->type,
            'name' => $request->name,
            'creator' => $sender,
            'course_id' => $request->course_id,
            'organization_id' => $user->organization_id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discussion  $discussion
     * @return \Illuminate\Http\Response
     */
    public function show(Discussion $discussion)
    {
        return $discussion->with('discussionmessage');
    }

    public function getdiscussion($id)
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        } else {
            $user = auth('api')->user();
        }
        return  Discussion::where('id', $id)->with('discussionmessage')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Discussion  $discussion
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Discussion $discussion)
    {
        $discussion->type = $request->type;
        $discussion->name = $request->name;
        $discussion->creator = $request->creator;
        $discussion->course_id = $request->course_id;
        $discussion->save();
        return $discussion;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discussion  $discussion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discussion $discussion)
    {
        $discussion->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
