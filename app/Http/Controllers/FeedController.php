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
    public function guestfeeds()
    {
        return Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->paginate(15);
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
        $feeds = Feed::where('organization_id', $user->organization_id)->get()->toArray();
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
        $interests = Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->map(function ($i) {
            return json_decode($i->tags);
        })->filter(function ($tag) {
            return $tag;
        })->flatten(1)->map(function ($a) {
            return $a->text;
        })->unique();

        $feeds  = Feed::get()->map(function ($i) {
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
        $feeds = Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->map(function ($i) use ($interest) {
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
        $feeds = Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->filter(function ($f)
        use ($interests) {
            $tags = collect(json_decode($f->tags))->map(function ($t) {
                return $t->value;
            });

            $check = array_intersect($interests, $tags->toArray());
            return count($check);
        });
        return (new Collection($feeds->values()->all()))->paginate(15);
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



        $feeds = Feed::where('organization_id', 99)
            ->orWhereIn('facilitator_id', $facilitators)
            ->orWhereIn('user_id', $users)
            ->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')
            ->latest()
            ->get();
        return (new Collection($feeds->values()->all()))->paginate(15);
    }
    public function trendingFeedsByComments()
    {
        $sorted = Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->get()->sortByDesc(function ($f) {
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
            'organization_id' => $user->organization_id,
            'media' => $request->media,
            'url' => $request->url,
            'publicId' => $request->publicId,
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
