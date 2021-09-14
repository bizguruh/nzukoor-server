<?php

namespace App\Http\Controllers;

use App\Events\AddFeed;
use App\Http\Resources\ConnectionResource;
use App\Models\Facilitator;
use App\Models\Feed;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use App\Support\Collection;


class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function my_array_unique($array, $keep_key_assoc = false)
    {
        $duplicate_keys = array();
        $tmp = array();

        foreach ($array as $key => $val) {
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val))
                $val = (array)$val;

            if (!in_array($val, $tmp))
                $tmp[] = $val;
            else
                $duplicate_keys[] = $key;
        }

        foreach ($duplicate_keys as $key)
            unset($array[$key]);

        return $keep_key_assoc ? $array : array_values($array);
    }
    public function guestfeeds()
    {
        return Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->paginate(15);
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
        return (new Collection($mergedfeeds))->paginate(15);
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
        $feeds = Feed::where('tribe_id', null)->get()->toArray();
        $allfeeds = [];
        if (count($feeds)) {
            if (count($tags)) {
                foreach ($feeds as $key => $value) {
                    if (!is_null($value['tags'])) {

                        if (!is_null(json_decode($value['tags'])) && is_array(json_decode($value['tags'])) && count(json_decode($value['tags']))) {
                            $check =  array_intersect($tags, array_map(function ($a) {
                                return $a->value;
                            }, json_decode($value['tags'])));

                            if (count($check)) {
                                array_push($allfeeds, $value);
                            }
                        }
                    }
                }
            }
            return $allfeeds;
        }
        return $allfeeds;
    }
    public function getTrendingFeedInterest()
    {
        $interests = Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->map(function ($i) {
            return json_decode($i->tags);
        })->filter(function ($tag) {
            return $tag;
        })->flatten(1)->map(function ($a) {
            return $a->text;
        })->unique();

        $feeds  = Feed::where('tribe_id', null)->get()->map(function ($i) {
            if (!is_null($i->tags) && count(json_decode($i->tags)))
                $i->tags = collect(json_decode($i->tags))->map(function ($v) {
                    return $v->text;
                });
            else {
                $i->tags = null;
            }
            return $i;
        });

        $filteredFeeds =   $feeds->filter(function ($tag) {
            return $tag->tags;
        });

        $trend =   $interests->map(function ($in) use ($filteredFeeds) {

            $count =  count($filteredFeeds->filter(function ($feed) use ($in) {

                return \in_array($in, $feed->tags->toArray());
            }));
            return  [
                'name' => $in,
                'count' => $count

            ];
        });

        $res = $trend->sortByDesc(function ($a) {
            return $a['count'];
        });
        return $res->values()->all();
    }

    public function getSpecificFeed($interest)
    {
        $feeds = Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->map(function ($i) use ($interest) {
            if (!is_null($i->tags) && count(json_decode($i->tags)))
                $i->tags = collect(json_decode($i->tags))->map(function ($v) {
                    return $v->text;
                });
            else {
                $i->tags = null;
            }
            return $i;
        });
        $filteredFeeds =   $feeds->filter(function ($tag) {
            return $tag->tags;
        });

        $trend =   $filteredFeeds->filter(function ($in) use ($interest) {
            return \in_array($interest, $in->tags->toArray());
        });

        return (new Collection($trend->values()->all()))->paginate(15);
    }

    public function recentFeedsByInterest()
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
        $feeds = Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->filter(function ($f)
        use ($interests) {
            $tags = collect(json_decode($f->tags))->map(function ($t) {
                return $t->value;
            });

            $check = array_intersect($interests, $tags->toArray());
            return count($check);
        });
        $myfeeds = $user->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->toArray();
        $mergedfeeds = collect(array_merge($feeds->toArray(), $myfeeds))->sortByDesc(function ($a) {
            return $a['created_at'];
        });

        $removeDuplicate = $this->my_array_unique($mergedfeeds->toArray());
        return (new Collection($removeDuplicate))->paginate(15);
    }

    public function customFeeds()
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

        $myfeeds = $user->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->toArray();

        $feeds = Feed::orWhereIn('user_id', $users)
            ->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')
            ->latest()
            ->get()->toArray();

        $mergedfeeds = collect(array_merge($feeds, $myfeeds))->sortByDesc(function ($a) {
            return $a['created_at'];
        });
        $removeDuplicate = $this->my_array_unique($mergedfeeds->toArray());
        return (new Collection($removeDuplicate))->paginate(15);
    }
    public function trendingFeedsByComments()
    {
        $sorted = Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->sortByDesc(function ($f) {
            return count($f['comments']);
        });


        return (new Collection($sorted->values()->all()))->paginate(15);
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
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'media' => $request->media,
            'url' => $request->url,
            'publicId' => $request->publicId,
            'message' => $request->message,
            'tribe_id' => $request->tribe_id,
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
        return  $feed->load('admin', 'user', 'facilitator', 'comments', 'likes', 'stars');
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

        $feed->message = $request->message;
        $feed->save();
        return $feed;
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
