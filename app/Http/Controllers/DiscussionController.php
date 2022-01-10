<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tribe;
use App\Models\Discussion;
use App\Support\Collection;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Models\DiscussionMessage;
use Illuminate\Support\Facades\Cache;
use App\Models\DiscussionMessageComment;
use App\Notifications\NewTribeDiscussion;
use App\Http\Resources\DiscussionResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\TribeDiscussionResource;

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


        $discussions = Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->latest()->get();

        return Cache::tags(['discussions'])->remember('discussions', 3600, function () use ($discussions) {
            return $discussions;
        });
    }
    public function discussionmembers($id)
    {
        $discussion = Discussion::where('id', $id)->first();

        $usersmessage = DiscussionMessage::where('discussion_id', $id)->get()->pluck('user_id');
        $userscomment = DiscussionMessageComment::where('discussion_id', $id)->get()->pluck('user_id');
        $mergedusers = array_merge($usersmessage->values()->all(), $userscomment->values()->all());
        array_push($mergedusers, $discussion->user_id);

        $uniquearray = array_unique($mergedusers);
        return   collect($uniquearray)->map(function ($d) {

            return [
                'username' => User::find($d)->username,
                'id' => $d
            ];
        });
    }


    public function guestdiscussions()
    {
        return  DiscussionResource::collection(Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions', 'tribe')->latest()->paginate(15));
    }
    public function guestexplorediscussions()
    {
        return  Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions', 'tribe')->latest()->paginate(6);
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

        $users = $connections->filter(function ($a) {
            if ($a->follow_type == 'user') {
                return $a;
            }
        })->map(function ($f) {
            return $f->user_id;
        });
        return Discussion::orWhereIn('user_id', $users)
            ->with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')
            ->latest()
            ->get();
    }



    public function trenddiscussions()
    {

        $discussion = Discussion::with('user',  'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->latest()->get();
        $sorted = $discussion->sortByDesc(function ($a) {
            return count($a['discussionmessage']);
        });

        return (new Collection($sorted->values()->all()))->paginate(15);
    }
    public function tribetrenddiscussions(Tribe $tribe)
    {

        $discussion = $tribe->load('discussions')->discussions;
        $sorted = $discussion->sortByDesc(function ($a) {
            return count($a['discussionmessage']);
        });
        return TribeDiscussionResource::collection((new Collection($sorted->values()->all()))->paginate(15))->response()->getData(true);
    }

    public function interestdiscussions()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization', 'tribe')->user()) {
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

        if (is_null($user->interests)) return [];
        $interests = $user->interests;
        $discussion = Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->latest()->get();
        $result =   $discussion->filter(function ($a) use ($interests) {

            $tags = collect($a->tags)->map(function ($t) {
                return $t['value'];
            });

            $check = array_intersect($interests, $tags->toArray());
            return count($check);
        });

        return (new Collection($result->values()->all()))->paginate(15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'tags' => ' required',

        ]);
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender = 'user';
        }


        $tribe = Tribe::find($request->tribe_id);
        $data = $user->discussions()->create([
            'type' =>'public',
            'name' => $request->name,
            'tags' => $request->tags,
            'creator' => 'user',
            'description' => $request->description,
            'tribe_id' => $request->tribe_id,
            'organization_id' =>  1,
        ]);


        if ($request->type == 'private') {
            $user->privatediscusion()->create([
                'discussion_id' => $data->id,
                'type' => $sender
            ]);
        }

        $tribe = Tribe::find($request->tribe_id);
        if(is_null($tribe)) return response(404,'Tribe not found');
        $tribemembers = $tribe->users()->get()->filter(function ($a) use ($user) {
            return $a->id != $user->id;
        });
        $details = [
            'from_email' => 'nzukoor@gmail.com',
            'from_name' =>  $tribe->name . 'Tribe - Nzukoor',
            'greeting' => 'Hello ',
            'body' => 'New Tribe Discussion Alert! ' . $user->username . " just created a new discussion in" . $tribe->name . 'Tribe',
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => 'https://nzukoor.com/member/tribes',

        ];


        Notification::send($tribemembers, new NewTribeDiscussion($details));
        broadcast(new NotificationSent())->toOthers();
        return new DiscussionResource($data->load('user',  'discussionmessage', 'discussionvote', 'discussionview', 'tribe'));
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


        $alldiscussions =  Discussion::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->latest()->get();

        $discussion = Discussion::where('tribe_id', null)->find($id)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->first();

        $newdis = [];
        foreach ($alldiscussions as $key => $value) {
            $intersect =   array_intersect(sorttag($value->tags), sorttag($discussion->tags));
            if (count($intersect) > 0) {
                array_push($newdis, $value);
            }
        }

        $discussion->related = $newdis;

        return $discussion->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe');
    }

    public function getguestdiscussion($id)
    {
        function filtertag($arr)
        {
            return  array_map(function ($val) {
                return $val->value;
            }, $arr);
        }


        $alldiscussions =  Discussion::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions', 'tribe')->get();
        $discussion = Discussion::where('id', $id)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'contributions', 'tribe')->first();
        $related =  $alldiscussions->filter(function ($a) use ($discussion) {

            if (!is_null($a['tags']) && count($a['tags'])) {
                $interests = array_intersect(filtertag($discussion->tags), filtertag($a->tags));

                return count($interests);
            }
        });
        $discussion->related = $related->values()->all();
      

        return new DiscussionResource($discussion->load('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe'));
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


        //  $alldiscussions =  Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->latest()->get();

        // $related =  $alldiscussions->filter(function ($a) use ($discussion) {


        //     if (!is_null($a['tags']) && count($a['tags'])) {
        //         $interests = array_intersect(sorttags($discussion->tags), sorttags($a->tags));

        //         return count($interests);
        //     }
        // });
        // $discussion->related = $related->values()->all();
        $data =  new DiscussionResource($discussion->load('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe'));


            return $data;

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
        $user = auth('api')->user();
        if ($user->id != $discussion->user_id) return response('Unauthorised', 401);

        if ($request->has('type') && $request->filled('type') && !empty($request->input('type'))) {
            $discussion->type = $request->type;
        }
        if ($request->has('name') && $request->filled('name') && !empty($request->input('name'))) {
            $discussion->name = $request->name;
        }
        if ($request->has('category') && $request->filled('category') && !empty($request->input('category'))) {
            $discussion->category = $request->category;
        }
        if ($request->has('tags') && $request->filled('tags') && !empty($request->input('tags'))) {
            $discussion->tags = $request->tags;
        }
        if ($request->has('creator') && $request->filled('creator') && !empty($request->input('creator'))) {
            $discussion->creator = $request->creator;
        }
        if ($request->has('course_id') && $request->filled('course_id') && !empty($request->input('course_id'))) {
            $discussion->course_id = $request->course_id;
        }




        $discussion->save();
        return $discussion->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discussion  $discussion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discussion $discussion)
    {
        $user = auth('api')->user();
        if ($user->id != $discussion->user_id) return response('Unauthorised', 401);
        $discussion->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
