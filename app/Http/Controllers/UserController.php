<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Models\Admin;
use App\Models\Tribe;
use App\Mail\OtpReset;
use App\Models\Course;
use App\Jobs\WelcomeEmail;
use App\Models\Discussion;
use App\Mail\memberwelcome;
use App\Models\EnrollCount;
use App\Models\Facilitator;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\AccountDetail;
use App\Mail\PasswordResetMail;
use App\Models\CourseCommunity;
use Illuminate\Support\Facades\DB;
use App\Models\CourseCommunityLink;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\AddedToLibrary;
use Illuminate\Support\Facades\Cache;
use App\Notifications\SendNotification;
use App\Http\Controllers\MailController;
use App\Http\Resources\ConnectionResource;
use App\Http\Controllers\BankDetailController;
use App\Notifications\PrivateDiscussionCreated;

class UserController extends Controller
{

    protected function generateCode($numChars)
    {

        //Return the job id.
        return  mt_rand(1000, 9999);
    }


    public $ttl = 60 * 60 * 24;
    public function index()
    {
        return User::with('role')->get();
    }
    public function getuserbyusername($username)
    {
        $user = User::where(strtolower('username'), strtolower($username))->first();
        return  is_null($user) ? response()->json([
            'message' => 'not found'

        ])
            : response()->json([
                'message' => 'found',
                'data' => $user
            ]);
    }

    public function getuserbyid(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'found',
            'data' => new UserResource($user)
        ]);
    }

    public function userinfo($id)
    {
        return User::find($id)->load('role');
    }

    public function userfeeds($id)
    {
        $user = User::find($id);
        return   $user->feeds()->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->get();
    }

    public function userdiscussions($id)
    {
        $user =  User::find($id);
        return $user->discussions()->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get();
    }

    public function userevents($id)
    {
        $user =  User::find($id);
        return $user->event()->with('eventattendance')->latest()->get();
    }

    public function userconnections($id)
    {
        $user =  User::find($id);

        return  ConnectionResource::collection($user->connections()->latest()->get());
    }

    public function facilitatorgetusers()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('facilitator')->user();


        return User::where('organization_id', $user->organization_id)->with('loginhistory')->get();
    }

    public function facilitatorgetuser($id)
    {
        $ttl = 3600;

        return User::where('id', $id)->first();
    }


    public function store(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',

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
            'state' => 'Lagos',
            'verification' => false,
            'voice' => 49,
            'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
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
        $newuser->role = 'Member';
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

        ]);

        $result =  DB::transaction(function () use ($request) {

            $referral_code =  $this->generateCode(2);
            $check = User::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = User::where('referral_code', $referral_code)->first();
            }


            if ($request->has('referral') && $request->filled('referral') && !empty($request->input('referral'))) {


                if (User::where('referral_code', $request->referral)->first()) {
                    $olduser = User::where('referral_code', $request->referral)->first();
                    $ref = 'member';
                }


                $newuser = User::create([
                    'organization_id' =>  1,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'country' => 'NG',
                    'state' => 'Lagos',
                    'verification' => false,
                    'voice' => 49,
                    'username' => $request->username,
                    'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                ]);


                // Add referral


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
                    'referree_type' =>    $ref,
                    'referree_id'    =>  $newuser->id
                ]);




            } else {
                $newuser = User::create([

                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'country' => 'NG',
                    'state' => 'Lagos',
                    'verification' => false,
                    'voice' => 49,
                    'username' => $request->username,
                    'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                ]);

            }

            if ($request->tribe_id) {
                $tribe = Tribe::find($request->tribe_id);
                $tribe->users()->attach($newuser->id);
            }



            $details = [
                'greeting' => 'Welcome',
                'body' => "Welcome to Nzukoor, Find discussions, share ideas, stories, discover events and so much more, according to your personal interests.",
                'thanks' => 'Thanks',
                'actionText' => '',
                'url' => '',
                'to' => 'user',
                'id' => $newuser->id
            ];
            $newuser->notify(new SendNotification($details));
            // $mail = new MailController;
            // $mail->memberwelcome($newuser);
            WelcomeEmail::dispatch($newuser);
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
                'organization_id' => $user->organization_id ? $user->organization_id : 1,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'country' => 'NG',
                'state' => 'Lagos',
                'verification' => false,
                'voice' => 49,
                'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . $referral_code,
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

    public function adminStoreUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $result =  DB::transaction(function () use ($request) {
            $user = auth('admin')->user();
            $referral_code =  $this->generateCode(2);
            $check = User::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = User::where('referral_code', $referral_code)->first();
            }


            $newuser = User::create([
                'organization_id' => $user->organization_id ? $user->organization_id : 1,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'country' => 'NG',
                'state' => 'Lagos',
                'verification' => false,
                'voice' => 49,
                'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . $referral_code,
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

        $user->interests = $request->interest;
        $user->save();
        return $user;
    }


    public function show(User $user)
    {
        return $user->load('role', 'accountdetail');
    }


    public function update(Request $request,    User $user)
    {


        if ($request->account_no && $request->bank_name && $request->bank_code) {

            $verify  = new BankDetailController();
            $accountverification =  $verify->verifyaccountnumber($request->account_no, $request->bank_code);
            if (!$accountverification) {
                return response([
                    'status' => false,
                    'message' => 'Cannot verify account details'
                ], 500);
            }
            AccountDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'account_no' => $request->account_no,
                    'bank_code' => $request->bank_code,
                    'bank_name' => $request->bank_name,
                ]
            );
        }


        if ($request->has('name') && $request->filled('name') && !empty($request->input('name'))) {

            $user->name = $request->name;
        }


        if ($request->has('email') && $request->filled('email') && !empty($request->input('email'))) {
            $user->email = $request->email;
        }
        if ($request->has('username') && $request->filled('username') && !empty($request->input('username'))) {
            $user->username = $request->username;
        }
        if ($request->has('address') && $request->filled('address') && !empty($request->input('address'))) {
            $user->address = $request->address;
        }
        if ($request->has('phone') && $request->filled('phone') && !empty($request->input('phone'))) {
            $user->phone = $request->phone;
        }
        if ($request->has('bio') && $request->filled('bio') && !empty($request->input('bio'))) {
            $user->bio = $request->bio;
        }
        if ($request->has('profile') && $request->filled('profile') && !empty($request->input('profile'))) {
            $user->profile = $request->profile;
        }

        if ($request->has('state') && $request->filled('state') && !empty($request->input('state'))) {
            $user->state = $request->state;
        }
        if ($request->has('country') && $request->filled('country') && !empty($request->input('country'))) {
            $user->country = $request->country;
        }
        if ($request->has('age') && $request->filled('age') && !empty($request->input('age'))) {
            $user->age = $request->age;
        }
        if ($request->has('gender') && $request->filled('gender') && !empty($request->input('gender'))) {
            $user->gender = $request->gender;
        }
        if ($request->has('lga') && $request->filled('lga') && !empty($request->input('lga'))) {
            $user->lga = $request->lga;
        }
        if ($request->has('voice') && $request->filled('voice') && !empty($request->input('voice'))) {
            $user->voice = $request->voice;
        }
        if ($request->has('profile') && $request->filled('profile') && !empty($request->input('profile'))) {
            $user->username = $request->username;
        }
        if ($request->has('interests') && $request->filled('interests') && !empty($request->input('interests'))) {
            $user->interests = $request->interests;
        }
        if ($request->has('show_age') && $request->filled('show_age') && !empty($request->input('show_age'))) {
            $user->show_age = $request->show_age;
        }
        if ($request->has('show_name') && $request->filled('show_name') && !empty($request->input('show_name'))) {
            $user->show_name = $request->show_name;
        }
        if ($request->has('show_email') && $request->filled('show_email') && !empty($request->input('show_email'))) {
            $user->show_email = $request->show_email;
        }

        $user->save();

        return $user->load('accountdetail');
    }

    public function updatepassword(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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



    public function postEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);



        $token = Str::random(40);

        DB::table('password_resets')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );


        $credentials = $request->only(["email"]);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {

            $responseMessage = "email not found";

            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }


        $maildata = [
            'title' => 'Password Reset',
            'url' => 'https://nzukoor.com/reset-password/?token=' . $token . '&action=password_reset&auth=' . $request->type
        ];

        Mail::to($credentials['email'])->send(new PasswordResetMail($maildata));
        return response()->json([
            "success" => true,
            "message" => 'email sent',

        ], 200);
    }
    public function resetPassword(Request $request)
    {


        $request->validate([
            // 'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'confirmpassword' => 'required',

        ]);

        $updatePassword = DB::table('password_resets')
            ->where(['token' => $request->token])
            ->first();

        if (!$updatePassword) {
            return response()->json([
                "success" => false,
                "message" => 'Invalid request'

            ], 500);
        }

        if ($request->type == 'user') {
            $oldpassword = User::where('email', $updatePassword->email)->first()->password;
            $checkpassword = Hash::check($request->password, $oldpassword);
            if ($checkpassword) {
                return response()->json([
                    "success" => false,
                    "message" => 'identical password'

                ], 422);
            }

            $user = User::where('email', $updatePassword->email)
                ->update(['password' => Hash::make($request->password)]);
        }
        if ($request->type == 'facilitator') {
            $oldpassword = Facilitator::where('email', $updatePassword->email)->first()->password;
            $checkpassword = Hash::check($request->password, $oldpassword);
            if ($checkpassword) {
                return response()->json([
                    "success" => false,
                    "message" => 'identical password'

                ], 422);
            }

            $user = Facilitator::where('email', $updatePassword->email)
                ->update(['password' => Hash::make($request->password)]);
        }
        if ($request->type == 'admin') {
            $oldpassword = Admin::where('email', $updatePassword->email)->first()->password;
            $checkpassword = Hash::check($request->password, $oldpassword);
            if ($checkpassword) {
                return response()->json([
                    "success" => false,
                    "message" => 'identical password'

                ], 200);
            }

            $user = Admin::where('email', $updatePassword->email)
                ->update(['password' => Hash::make($request->password)]);
        }

        if ($request->type == 'organization') {
            $oldpassword = Organization::where('email', $updatePassword->email)->first()->password;
            $checkpassword = Hash::check($request->password, $oldpassword);
            if ($checkpassword) {
                return response()->json([
                    "success" => false,
                    "message" => 'identical password'

                ], 200);
            }

            $user = Organization::where('email', $updatePassword->email)
                ->update(['password' => Hash::make($request->password)]);
        }


        DB::table('password_resets')->where(['token' => $request->token])->delete();

        return response()->json([
            "success" => true,
            "message" => 'Your password has been changed'

        ], 200);
    }

    public function createotp(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
        ]);

        $user =  User::where('email', $request->email)->first();

        if (is_null($user)) {

            return response([
                'status' => false,
                'message' => 'Email not found'
            ], 500);
        }
        $code = mt_rand(100000, 999999);

        $otp = Otp::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $code]
        );
        $otp->save();
        $maildata = [
            'code' => $code
        ];


        Mail::to($user)->send(new OtpReset($maildata));
        return response()->json([
            "success" => true,
            "message" => 'otp sent to email'

        ], 200);
    }

    public function changePasswordByOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|min:6|max:6',
            'password' => 'required|string|min:6',
            'confirmpassword' => 'required',
        ]);
        $user_id  = Otp::where('code', $request->code)->value('user_id');

        if (!$user_id) {
            return response()->json([
                "success" => false,
                "message" => 'Invalid code'

            ], 200);
        }

        $user = User::find($user_id);
        $oldpassword = $user->password;
        $checkpassword = Hash::check($request->password, $oldpassword);
        if ($checkpassword) {
            return response()->json([
                "success" => false,
                "message" => 'identical password'

            ], 200);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Otp::where('code', $request->code)->first()->delete();

        return response()->json('Password changed');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
