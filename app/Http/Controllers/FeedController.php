<?php

namespace App\Http\Controllers;

use App\Events\AddFeed;
use App\Http\Resources\ConnectionResource;
use App\Models\Facilitator;
use App\Models\Feed;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guestfeeds()
    {
        return Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->get();
    }
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

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
        $connection = $user->connections()->get()->toArray();
        $connections = ConnectionResource::collection($connection);
        $myfeeds = $user->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->toArray();


        $newarr =  array_map(function ($a) {
            if ($a['follow_type'] == 'user') {
                $u = User::find($a['following_id']);
                return $u->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get();
            }
            if ($a['follow_type'] == 'facilitator') {
                $u = Facilitator::find($a['following_id']);
                return $u->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get();
            }
        }, $connection);
        $filterArray = array_filter($newarr, function ($a) {
            if (count($a)) {
                return $a;
            }
        });
        $singleArray = [];

        foreach ($filterArray as $child) {
            foreach ($child as $value) {
                $singleArray[] = $value;
            }
        }
        $mergedfeeds =  array_merge($myfeeds, $singleArray);
        usort($mergedfeeds, function ($a, $b) {
            return new DateTime($b['created_at']) <=> new DateTime($a['created_at']);
        });
        return $mergedfeeds;


        // $notFlat = [[1, 2], [3, 4]];
        // // $filterArray
        // $flat = array_merge($filterArray);
        // return  $flat;


        //  return Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->where('organization_id', $user->organization_id)->latest()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMyFeeds()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

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

        return  $connections = $user->connections()->toArray();
    }

    public function getFeedsByInterest()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }


        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $tags = $user->interests ? json_decode($user->interests) : [];
        $feeds = Feed::where('organization_id', $user->organization_id)->get()->toArray();
        $allfeeds = [];
        if (count($tags)) {
            foreach ($feeds as $key => $value) {
                if (!is_null($value['tags'])) {


                    $check =  array_intersect($tags, array_map(function ($a) {
                        return $a->value;
                    }, json_decode($value['tags'])));

                    if (count($check)) {
                        array_push($allfeeds, $value);
                    }
                }
            }
        }
        return $allfeeds;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

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


        $data = $user->feeds()->create([
            'organization_id' => $user->organization_id,
            'media' => $request->media,
            'url' => $request->url,
            'message' => $request->message,
            'tags' => json_encode($request->tags)
        ]);
        broadcast(new AddFeed($user, $data->load('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')))->toOthers();
        return $data->load('admin', 'user', 'facilitator', 'comments', 'likes', 'stars');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function show(Feed $feed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function edit(Feed $feed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feed $feed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feed  $feed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feed $feed)
    {
        $feed->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
