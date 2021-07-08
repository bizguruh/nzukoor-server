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
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        return Discussion::where('organization_id', $user->organization_id)
            ->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
    }

    public function guestdiscussions()
    {
        return Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions')->latest()->get();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $sender = 'admin';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $sender = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender = 'user';
        }

        $request->all();

        $data = $user->discussions()->create([
            'type' => $request->type,
            'name' => $request->name,
            'tags' => json_encode($request->tags),
            'creator' => $sender,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'organization_id' => $user->organization_id,
        ]);


        if ($request->type == 'private') {
            $user->privatediscusion()->create([
                'discussion_id' => $data->id,
                'type' => $sender
            ]);
        }
        return $data->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discussion  $discussion
     * @return \Illuminate\Http\Response
     */
    public function guestdiscussion($id)
    {
        function sorttag($arr)
        {
            return  array_map(function ($val) {
                return $val->value;
            }, $arr);
        }


        $alldiscussions =  Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();

        $discussion = Discussion::find($id)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->first();

        $newdis = [];
        foreach ($alldiscussions as $key => $value) {
            $intersect =   array_intersect(sorttag(json_decode($value->tags)), sorttag(json_decode($discussion->tags)));
            if (count($intersect) > 0) {
                array_push($newdis, $value);
            }
        }

        $discussion->related = $newdis;

        return $discussion->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview');
    }
    public function show(Discussion $discussion)
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
        function sorttags($arr)
        {
            return  array_map(function ($val) {
                return $val->value;
            }, $arr);
        }


        $alldiscussions =  Discussion::where('organization_id', $user->organization_id)
            ->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();

        $newdis = [];
        foreach ($alldiscussions as $key => $value) {
            $intersect =   array_intersect(sorttags(json_decode($value->tags)), sorttags(json_decode($discussion->tags)));
            if (count($intersect) > 0) {
                array_push($newdis, $value);
            }
        }

        $discussion->related = $newdis;

        return $discussion->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview');
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
        $discussion->tags = json_encode($request->tags);
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
