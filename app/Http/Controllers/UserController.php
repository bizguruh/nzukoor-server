<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Course;
use App\Models\CourseCommunity;
use App\Models\CourseCommunityLink;
use App\Models\EnrollCount;
use App\Models\Facilitator;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\AddedToLibrary;
use App\Notifications\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected function generateCode($numChars)
    {

        //Return the job id.
        return  mt_rand(1000, 9999);
    }


    public function index()
    {
        return User::all();
    }


    public function facilitatorgetusers()
    {
        $user = auth('facilitator')->user();
        return User::where('organization_id', $user->organization_id)->with('loginhistory')->get();
    }

    public function facilitatorgetuser($id)
    {
        return User::where('id', $id)->first();
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $user = auth('organization')->user();
        $referral_code =  $this->generateCode(2);
        $check = $user->user()->where('referral_code', $referral_code)->first();
        while (!is_null($check)) {
            $referral_code =  $this->generateCode(2);
            $check = $user->user()->where('referral_code', $referral_code)->first();
        }


        $newuser = $user->user()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'profile' => $request->profile,
            'country' => 'NG',
            'verification' => false,
            'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,
        ]);
        $details = [
            'greeting' => 'Welcome',
            'body' => "Welcome to " . $user->name . ", Find facilitators, courses,events according to your personal interests.",
            'thanks' => 'Thanks',
            'actionText' => '',
            'url' => '',
            'to' => 'user',
            'id' => $newuser->id,
            'email' => $request->email,
            'password' => $request->password
        ];







        $newuser->notify(new SendNotification($details));
        $newuser->role = 'Learner';
        $newuser->password = $request->password;

        $mail =  new MailController;
        $mail->sendroleinvite($user->name, $newuser);
        return response($newuser->load('loginhistory'), 201);
    }

    public function storeuser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $result =  DB::transaction(function () use ($request) {
            $user = auth('api')->user();
            $referral_code =  $this->generateCode(2);
            $check = User::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = User::where('referral_code', $referral_code)->first();
            }


            if ($request->referral) {
                if (CourseCommunityLink::where('code', $request->referral)->first()) {
                    $referral_type = 'group_course';
                } else {
                    $referral_type = 'normal';
                }
                if ($referral_type == 'normal') {
                    if (User::where('referral_code', $request->referral)->with('organization')->first()) {
                        $olduser = User::where('referral_code', $request->referral)->with('organization')->first();
                    } else {
                        $olduser = Facilitator::where('referral_code', $request->referral)->with('organization')->first();
                    }
                }
                if ($referral_type == 'group_course') {
                    $link = CourseCommunityLink::where('code', $request->referral)->first();
                    $olduser = User::find($link->user_id)->first();
                    $co = Course::find($link->course_id);
                }
                $referree_type = 'learner';

                $newuser = User::create([
                    'organization_id' => $olduser->organization_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'country' => 'NG',
                    'verification' => false,
                    'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,
                ]);


                // Add referral

                if ($referral_type == 'normal') {
                    $referral_detail = [
                        'greeting' => 'Welcome',
                        'body' => $newuser->name . " just used your referral link to create an account",
                        'thanks' => 'Thanks',
                        'actionText' => '',
                        'url' => '',
                        'to' => 'user',
                        'id' => $newuser->id
                    ];
                    $olduser->notify(new SendNotification($referral_detail));
                    $olduser->referral()->create([
                        'referree_type' =>    $referree_type,
                        'referree_id'    =>  $newuser->id
                    ]);
                }


                if ($referral_type == 'group_course') {
                    $referral_detail = [
                        'greeting' => 'Welcome',
                        'body' => $newuser->name . " accepted your invitation to take the course titled " . $co->title . " with you",
                        'thanks' => 'Thanks',
                        'actionText' => '',
                        'url' => '',
                        'to' => 'user',
                        'id' => $newuser->id
                    ];
                    $olduser->notify(new SendNotification($referral_detail));

                    $olduser->referral()->create([
                        'referree_type' =>    'learner',
                        'referree_id'    =>  $newuser->id
                    ]);
                    $newuser->coursecommunity()->create([
                        'code' => $request->referral,
                        'course_id' => $link->course_id
                    ]);


                    $link_users = CourseCommunity::where('code', $request->referral)->get();
                    if (count($link_users) == $link->amount) {
                        $course = Course::find($link->course_id)->first();
                        $discussion = $olduser->discussions()->create([
                            'type' => 'private',
                            'name' => $request->referral,
                            'tags' => json_encode([]),
                            'creator' => 'user',
                            'description' => $course->description,
                            'course_id' => $course->id,
                            'organization_id' => $olduser->organization_id,
                        ]);

                        foreach ($link_users as $key => $value) {
                            $info = User::find($value->user_id);
                            $info->library()->create([
                                'course_id' => $link->course_id
                            ]);
                            $enroll = EnrollCount::where('course_id', $link->course_id)->where('organization_id', $info->organization_id)->first();

                            if (is_null($enroll)) {
                                EnrollCount::create([
                                    'course_id' => $link->course_id,
                                    'organization_id' => $info->organization_id,
                                    'count' => 1
                                ]);
                            } else {
                                $enroll->count = $enroll->count + 1;
                                $enroll->save();
                            }

                            $details = [
                                'body' =>  \ucfirst(Course::find($link->course_id)->title) . ' course has just been added to your library',
                            ];

                            $info->notify(new AddedToLibrary($details));

                            $info->privatediscusion()->create([
                                'discussion_id' => $discussion->id,
                                'type' => 'user'
                            ]);
                        }
                    }
                }
            } else {
                $newuser = User::create([
                    'organization_id' => null,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'country' => 'NG',
                    'verification' => false,
                    'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,
                ]);
            }

            $details = [
                'greeting' => 'Welcome',
                'body' => "Welcome to " . $olduser->organization->name . ", Find facilitators, courses,events according to your personal interests.",
                'thanks' => 'Thanks',
                'actionText' => '',
                'url' => '',
                'to' => 'user',
                'id' => $newuser->id
            ];
            $newuser->notify(new SendNotification($details));
            return $newuser;
        });


        return response($result->load('loginhistory'), 201);
    }

    public function facilitatorStoreUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $result =  DB::transaction(function () use ($request) {
            $user = auth('facilitator')->user();
            $referral_code =  $this->generateCode(2);
            $check = User::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = User::where('referral_code', $referral_code)->first();
            }


            $newuser = User::create([
                'organization_id' => $user->organization_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'country' => 'NG',
                'verification' => false,
                'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . $referral_code,
            ]);


            $details = [
                'greeting' => 'Welcome',
                'body' => "Welcome to " . $newuser->organization->name . ", Find facilitators, courses,events according to your personal interests.",
                'thanks' => 'Thanks',
                'actionText' => '',
                'url' => '',
                'to' => 'user',
                'id' => $newuser->id
            ];
            $newuser->notify(new SendNotification($details));
            return $newuser;
        });


        return response($result->load('loginhistory'), 201);
    }
    public function saveinterests(Request $request)
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

        $user->interests = json_encode($request->interest);
        $user->save();
        return $user;
    }


    public function show(User $user)
    {
        return $user;
    }


    public function update(Request $request,    User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->profile = $request->profile;
        $user->state = $request->state;
        $user->country = $request->country;
        $user->age = $request->age;
        $user->gender = $request->gender;
        $user->lga = $request->lga;
        $user->verification = $request->verification;
        $user->save();
        return $user;
    }

    public function updatepassword(Request $request)
    {


        if (auth('organization')->user()) {
            $user = auth('admin')->user();
            $find = Organization::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $find = Admin::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $find = Facilitator::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $find = User::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
    }

    public function resetpassword(Request $request)
    {

        if ($request->role == 'organization') {
            $user = auth('admin')->user();
            $find = Organization::where('email', $user->email);
        }
        if ($request->role == 'admin') {
            $user = auth('admin')->user();
            $find = Admin::where('email', $user->email);
        }
        if ($request->role == 'facilitator') {
            $user = auth('facilitator')->user();
            $find = Facilitator::where('email', $user->email);
        }
        if ($request->role == 'user') {
            $user = auth('api')->user();
            $find = User::where('email', $user->email);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
