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

        return Discussion::where('organization_id', $user->organization_id)
            ->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
    }


    public function guestdiscussions()
    {
        return Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions')->latest()->get();
    }

    public function customdiscussions()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }

        $connections = $user->connections()->get();
        $facilitators = $connections->filter(function ($a) {
            if ($a->follow_type == 'facilitator') {
                return $a;
            }
        })->map(function ($f) {
            return $f->facilitator_id;
        });

        $users = $connections->filter(function ($a) {
            if ($a->follow_type == 'user') {
                return $a;
            }
        })->map(function ($f) {
            return $f->user_id;
        });
        return Discussion::where('organization_id', 99)
            ->orWhereIn('facilitator_id', $facilitators)
            ->orWhereIn('user_id', $users)
            ->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')
            ->latest()
            ->get();
    }


    public function trenddiscussions()
    {

        $discussion = Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->get();
        $sorted = $discussion->sortByDesc(function ($a) {
            return count($a['discussionmessage']);
        });
        return $sorted->values()->all();
    }

    public function interestdiscussions()
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

        if (is_null($user->interests)) return;
        $interests = json_decode($user->interests);
        $discussion = Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
        $result =   $discussion->filter(function ($a) use ($interests) {
            $tags = collect(json_decode($a->tags))->map(function ($t) {
                return $t->value;
            });

            $check = array_intersect($interests, $tags->toArray());
            return count($check);
        });

        return $result->values()->all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

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
            $sender = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender = 'user';
        }



        $data = $user->discussions()->create([
            'type' => $request->type,
            'name' => $request->name,
            'category' => json_encode($request->category),
            'tags' => json_encode($request->tags),
            'creator' => $sender,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
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

    public function getguestdiscussion($id)
    {
        return Discussion::where('id', $id)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions')->first();
    }
    public function show(Discussion $discussion)
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
        function sorttags($arr)
        {
            return  array_map(function ($val) {
                return $val->value;
            }, $arr);
        }


        $alldiscussions =  Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();

        $related =  $alldiscussions->filter(function ($a) use ($discussion) {


            if (!is_null($a['tags']) && count(json_decode($a['tags']))) {
                $interests = array_intersect(sorttags(json_decode($discussion->tags)), sorttags(json_decode($a->tags)));

                return count($interests);
            }
        });
        $discussion->related = $related->values()->all();
        return $discussion->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview');
    }

    public function getdiscussion($id)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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
        $discussion->category = json_encode($request->category);
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
