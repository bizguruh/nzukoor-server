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


        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $check = Connection::where([['follow_type', $request->follow_type], ['following_id', $request->following_id]])->first();
        if (is_null($check)) {
            return  $user->connections()->create([
                'follow_type' => $request->follow_type,
                'following_id' => $request->following_id
            ]);
        }
    }

    public function getlearnerswithinterests()
    {
        $user = auth('api')->user();
        $myusers = User::where('organization_id', $user->organization_id)->where('id', '!=', $user->id)->get();

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
        $user = auth('api')->user();
        $myusers = Facilitator::where('organization_id', $user->organization_id)->get();
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

    public function getidenticaldiscusiions()
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
        $discussions = Discussion::where('organization_id', $user->organization_id)->get();
        $interests = $user->interests ? json_decode($user->interests) : [];
        $allusers = [];

        if (count($interests)) {
            foreach ($discussions as $key => $value) {

                if (!is_null($value->tags)) {
                    $dis = array_map(function ($a) {
                        return $a->value;
                    }, json_decode($value->tags));


                    $check =  array_intersect($interests, $dis);
                    if (count($check)) {
                        $value->similar = count($check);
                        array_push($allusers, $value->load('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview'));
                    }
                }
            }
        }
        return $allusers;
    }

    public function getidenticalcourses()
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
        $courses = Course::where('organization_id', $user->organization_id)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire')->get();
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
}
