<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConnectionResource;
use App\Models\Connection;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Facilitator;
use App\Models\User;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
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


        return ConnectionResource::collection($user->connections()->latest()->get());
    }


    public function store(Request $request)
    {



        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
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

        $check = $user->connections()->where([['follow_type', $request->follow_type], ['following_id', $request->following_id]])->first();
        if (is_null($check)) {
            return  $user->connections()->create([
                'follow_type' => $request->follow_type,
                'following_id' => $request->following_id
            ]);
        }
    }

    public function getmemberswithinterests()
    {
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $myusers = User::where('organization_id', $user->organization_id)->get();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $myusers = User::where('organization_id', $user->organization_id)->where('id', '!=', $user->id)->get();
        }

        $users =  array_filter(json_decode(json_encode($myusers)), function ($a) use ($user) {
            $connection = $user->connections()->where('follow_type', 'user')->where('following_id', $a->id)->first();
            if (is_null($connection)) {

                return $a;
            }
        });

        $interests = $user->interests ? json_decode($user->interests) : [];
        $allusers = [];
        if (count($interests)) {
            foreach ($users as $key => $value) {
                if (!is_null($value->interests)) {
                    $check =  array_intersect($interests, json_decode($value->interests));
                    if (count($check)) {
                        $value->similar = count($check);
                        array_push($allusers, $value);
                    }
                }
            }
        }
        return $allusers;
    }

    public function getfacilitatorswithinterests()
    {

        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $myusers = Facilitator::where('organization_id', $user->organization_id)->where('id', '!=', $user->id)->get();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $myusers = Facilitator::where('organization_id', $user->organization_id)->get();
        }

        $users =  array_filter(json_decode(json_encode($myusers)), function ($a) use ($user) {
            $connection = $user->connections()->where('follow_type', 'facilitator')->where('following_id', $a->id)->first();
            if (is_null($connection)) {

                return $a;
            }
        });
        $interests = $user->interests ? json_decode($user->interests) : [];
        $allusers = [];
        if (count($interests)) {
            foreach ($users as $key => $value) {
                if (!is_null($value->interests)) {
                    $check =  array_intersect($interests, json_decode($value->interests));
                    if (count($check)) {
                        $value->similar = count($check);
                        array_push($allusers, $value);
                    }
                }
            }
        }
        return $allusers;
    }

    public function getotherswithinterests()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }


        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $userconnection = $user->connections()->get();

            $connectedusers = $user->connections()->get()->filter(function ($u) {
                if ($u->follow_type == 'user')  return $u;
            })->map(function ($u) {

                return $u->following_id;
            });
            $connectedfacilitators = $user->connections()->get()->filter(function ($u) {
                if ($u->follow_type == 'facilitator')  return $u;
            })->map(function ($u) {

                return $u->following_id;
            });


            $allusers = User::whereNotIn('id', $connectedusers->toArray())->get();
            $allfacilitators = Facilitator::where('id', '!=', $user->id)->whereNotIn('id', $connectedfacilitators->toArray())->get();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $connectedusers = $user->connections()->get()->filter(function ($u) {
                if ($u->follow_type == 'user')  return $u;
            })->map(function ($u) {

                return $u->following_id;
            });
            $connectedfacilitators = $user->connections()->get()->filter(function ($u) {
                if ($u->follow_type == 'facilitator')  return $u;
            })->map(function ($u) {

                return $u->following_id;
            });


            $allusers = User::where('id', '!=', $user->id)->whereNotIn('id', $connectedusers->toArray())->get();
            $allfacilitators = Facilitator::whereNotIn('id',  $connectedfacilitators->toArray())->get();
        }
        if (is_null($user->interests)) return;
        $interests = json_decode($user->interests);
        $similarUsers = $allusers->filter(function ($f)
        use ($interests) {
            $userinterests = json_decode($f->interests) ? json_decode($f->interests) : [];
            $check = array_intersect($interests, $userinterests);
            return count($check);
        });
        $similarFacilitators = $allfacilitators->filter(function ($f)
        use ($interests) {
            $userinterests = json_decode($f->interests) ? json_decode($f->interests) : [];
            $check = array_intersect($interests, $userinterests);
            return count($check);
        });

        $mapsimilarusers = $similarUsers->map(function ($a) use ($interests) {

            $a->similar = count(array_intersect($interests, json_decode($a->interests)));
            return $a;
        });
        $mapsimilarfacilitators = $similarFacilitators->map(function ($a) use ($interests) {

            $a->similar = count(array_intersect($interests, json_decode($a->interests)));
            return $a;
        });

        $mergedUsers = array_merge($mapsimilarfacilitators->values()->all(), $mapsimilarusers->values()->all());
        return $mergedUsers;
    }
    public function getUsersWithInterest($interest)
    {


        $allusers = User::get()->filter(function ($a) {
            return $a->interests && count(json_decode($a->interests));
        });
        $allfacilitators = Facilitator::get()->filter(function ($a) {
            return $a->interests && count(json_decode($a->interests));
        });



        $similarUsers = $allusers->filter(function ($a) use ($interest) {
            $userinterests = json_decode($a->interests) ? json_decode($a->interests) : [];
            $mappedusers = collect($userinterests)->map(function ($f) {
                return strtolower($f);
            });
            return  in_array(strtolower($interest), $mappedusers->toArray());
        });
        $similarFacilitators = $allfacilitators->filter(function ($a) use ($interest) {
            $userinterests = json_decode($a->interests) ? json_decode($a->interests) : [];
            $mappedusers = collect($userinterests)->map(function ($f) {
                return strtolower($f);
            });
            return  in_array(strtolower($interest), $mappedusers->toArray());
        });

        $mergedUsers = array_merge($similarFacilitators->values()->all(), $similarUsers->values()->all());
        return $mergedUsers;
    }


    public function getidenticaldiscusiions()
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
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
        $discussion = Discussion::where('organization_id', $user->organization_id)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
        $result =   $discussion->filter(function ($a) use ($interests) {
            $tags = collect(json_decode($a->tags))->map(function ($t) {
                return $t->value;
            });

            $check = array_intersect($interests, $tags->toArray());
            return count($check);
        });
        return $result->values()->all();
    }

    public function getidenticalcourses()
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
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
        $courses = Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->get();
        $interests = $user->interests ? json_decode($user->interests) : [];
        $allusers = [];

        if (count($interests)) {
            foreach ($courses as $key => $value) {

                if (!is_null($value->courseoutline)) {
                    $dis = json_decode($value->courseoutline->knowledge_areas)->value;
                    $check =  in_array($dis, $interests);
                    if ($check) {
                        array_push($allusers, $value);
                    }
                }
            }
        }
        return $allusers;
    }



    public function deleteconnection(Request $request, $id)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
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
        $check = $user->connections()->where([['follow_type', $request->follow_type], ['following_id', $request->following_id]])->first();
        $check->delete();
    }
    public function destroy(Connection $connection)
    {

        $connection->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
