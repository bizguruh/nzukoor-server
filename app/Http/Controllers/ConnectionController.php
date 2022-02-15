<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inbox;
use App\Models\Course;
use App\Models\Connection;
use App\Models\Discussion;
use App\Events\SearchEvent;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\UserResource;
use App\Http\Resources\FollowerResource;
use App\Models\PendingConnectionMessage;
use App\Http\Resources\ChatUsersResource;
use App\Http\Resources\ConnectionResource;
use App\Http\Resources\PendingConnectionResource;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        if (auth('api')->user()) {
            $user = auth('api')->user();
        }


        return ConnectionResource::collection($user->connections()->latest()->get());
    }
    public function chatusers()
    {

        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $data = Connection::where('user_id', $user->id)->with('user')->latest()->get();
        $res = ChatUsersResource::collection($data);
        return  $results = collect($res)->sortByDesc('last_message_time')->values()->all();
    }
    public function pendingchatusers()
    {

        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $data = PendingConnectionMessage::where('user_id', $user->id)->with('user')->latest()->get();
        $res = ChatUsersResource::collection($data);
        return  $results = collect($res)->sortByDesc('last_message_time')->values()->all();
    }
    public function pendingconections()
    {

        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $data = $user->pendingconnections()->get();
        return PendingConnectionResource::collection($data);
    }
    public function anonymousmessage()
    {
        $user =  auth('api')->user();
        $messages = Inbox::where('receiver_id', $user->id)->get();
    }
    public function myconnections()
    {
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $type = 'user';
        }

        $data = Connection::where('following_id', $user->id)->latest()->get();
        return  FollowerResource::collection($data);
    }


    public function store(Request $request)
    {


        $user = auth('api')->user();
        $check = $user->connections()->where('following_id', $request->following_id)->first();
        if (is_null($check)) {
            $connection =  $user->connections()->create([
                'follow_type' => 'user',
                'following_id' => $request->following_id
            ]);
        }

        $checkpending = $user->pendingconnections()->where('following_id', $request->following_id)->first();
        if (!is_null($checkpending)) {

            $checkpending->delete();
        }

        $isFollowingBack = Connection::where([['user_id', $request->following_id],['following_id', $user->id]])->first();
        if (is_null($isFollowingBack)) {
            PendingConnectionMessage::create([
                'following_id' => $user->id,
                'user_id' => $request->following_id
            ]);
        }

        return response([
            'status' => 'success'
        ], 200);
    }
    public function deletepending(Request $request)
    {

        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        $checkpending = $user->pendingconnections()->where('following_id', $request->following_id)->first();
        $checkpending->delete();
        return response([
            'status'=>'success'
        ],200);
    }

    public function removeconnection( $id)
    {
        PendingConnectionMessage::find($id)->delete();
        return response([
            'status' => 'success'
        ], 200);
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

        $interests = $user->interests;
        $allusers = [];
        if (count($interests)) {
            foreach ($users as $key => $value) {
                if (!is_null($value->interests)) {
                    $check =  array_intersect($interests, $value->interests);
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
        $interests = $user->interests ? $user->interests : [];
        $allusers = [];
        if (count($interests)) {
            foreach ($users as $key => $value) {
                if (!is_null($value->interests)) {
                    $check =  array_intersect($interests, $value->interests);
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



        $user = auth('api')->user();
        $connectedusers = $user->connections()->get()->filter(function ($u) {
            if ($u->follow_type == 'user')  return $u;
        })->map(function ($u) {

            return $u->following_id;
        });



        $allusers = User::where('id', '!=', $user->id)->whereNotIn('id', $connectedusers->toArray())->inRandomOrder()->get();


        if (is_null($user->interests)) return;
        $interests = $user->interests;
        $similarUsers = $allusers->filter(function ($f)
        use ($interests) {
            $userinterests = $f->interests ? $f->interests : [];
            $check = array_intersect($interests, $userinterests);
            return count($check);
        });


        $mapsimilarusers = $similarUsers->map(function ($a) use ($interests) {

            $a->similar = count(array_intersect($interests, $a->interests));
            return $a;
        });


        $mergedUsers = $mapsimilarusers->values()->all();
        return array_slice($mergedUsers, 0, 10);
    }
    public function getUsersWithInterest($interest)
    {


        $allusers = User::get()->filter(function ($a) {
            return $a->interests && count($a->interests);
        });
        $allfacilitators = Facilitator::get()->filter(function ($a) {
            return $a->interests && count($a->interests);
        });



        $similarUsers = $allusers->filter(function ($a) use ($interest) {
            $userinterests = $a->interests ? $a->interests : [];
            $mappedusers = collect($userinterests)->map(function ($f) {
                return strtolower($f);
            });
            return  in_array(strtolower($interest), $mappedusers->toArray());
        });
        $similarFacilitators = $allfacilitators->filter(function ($a) use ($interest) {
            $userinterests = $a->interests ? $a->interests : [];
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
        $interests = $user->interests;
        $discussion = Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
        $result =   $discussion->filter(function ($a) use ($interests) {
            $tags = collect($a->tags)->map(function ($t) {

                return $t['value'];
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
        $interests = $user->interests ? $user->interests : [];
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



    public function search(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('username', 'like', '%' . $query . '%')->get();

        //broadcast search results with Pusher channels
        event(new SearchEvent(UserResource::collection($users)));
        return response()->json("ok");
    }
    public function searchconnections(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('username', 'like', '%' . $query . '%')->get();

        //broadcast search results with Pusher channels

        return UserResource::collection($users);
    }

    //fetch all products
    public function get(Request $request)
    {
        $users = User::get();
        return response()->json($users);
    }
}
