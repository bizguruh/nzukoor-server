<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tribe;
use App\Mail\ContactMail;
use App\Mail\EventInvite;
use App\Mail\TribeInvite;
use App\Models\Discussion;
use App\Mail\ReferralInvite;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Mail\DiscussionInvite;
use App\Mail\GroupCourseInvite;
use App\Events\NotificationSent;
use App\Notifications\RoleInvite;
use App\Notifications\GroupInvite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class MailController extends Controller
{
    public function sendroleinvite($organization, $user)
    {


        $userorg = auth('organization')->user();
        $name = trim($user->username);

        if ($user->role == 'admin') {
            $body = 'You have been invited by ' . $organization . ' to be an ' . $user->role . ' on Nzukoor';
        } else {
            $body = 'You have been invited by ' . $organization . ' to be a ' . $user->role . ' on Nzukoor';
        }

        $details = [
            'from_email' => 'info@nzukoor.com',
            'from_name' => 'Nzukoor Team',
            'greeting' => 'Hello ' . $name,
            'body' => $body,
            'actionText' => 'Click to login',
            'url' => "https://nzukoor.com/login",
            'code' => $userorg->referral_code,
            'email' => $user->email,
            'password' => $user->password

        ];

        $user->notify(new RoleInvite($details));
        broadcast(new NotificationSent());
    }
    public function sendwelcome($info)
    {

        $data = [
            'name' => $info->name,
            'referral_code' => $info->referral_code
        ];
        Mail::send('email.organizationwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject('Here’s Your Passport To Be More ');
            $message->from('info@nzukoor.com', 'Nzukoor Team');
        });
    }

    public function sendfacilitatorwelcome($info)
    {

        $data = [
            'name' => $info->name
        ];
        Mail::send('email.facilitatorwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject(' A Home For You To Share');
            $message->from('info@nzukoor.com', 'Nzukoor Team');
        });
    }

    public function memberwelcome($info)
    {

        $data = [
            'name' => $info->name
        ];
        Mail::send('email.memberwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject(' A Home For You To Share');
            $message->from('info@nzukoor.com', 'Nzukoor Team');
        });
    }

    public function sendreferral(Request $request)
    {

            $user = auth('api')->user();


        $data = [
            'code' => $request->code,
            'name' => $user->username,
            'organization' => 'Nzukoor',
            'from' => 'info@nzukoor.com',
            'url' => 'https://nzukoor.com/register/?invite=' . $request->code
        ];
        Mail::to($request->emails)->send(new ReferralInvite($data));
    }


    public function sendtribeinvite(Request $request)
    {

        $tribe  = Tribe::find($request->id);
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $body = 'You have been invited to join my tribe, **' . $tribe->name .  '** on Nzukoor.';
            $title = 'Come Join My Tribe!';
        }
        $name = trim($user->username);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

        $url = 'https://nzukoor.com/explore?activity=join_tribe&tribe_id=' . $request->id;



        $details = [

            'title' => $title,
            'from_email' => 'info@nzukoor.com',
            'from_name' => 'Nzukoor Team',
            'greeting' => 'Hello Friend',
            'body' => $body,
            'actionText' => 'Click to join',
            'url' => $url,
            'sender' => $user->username,


        ];

        Mail::to($request->emails)->send(new TribeInvite($details));
        return response($details, 200);
    }


    public function guestsendcourseinvite(Request $request)
    {



        $body = 'Lets enroll for the course titled, **' . $request->title . '** on Nzukoor.';
        $title = ' I think You’d Want To See This!';





        $details = [

            'title' => $title,
            'from_email' => 'info@nzukoor.com',
            'from_name' => 'Nzukoor',
            'greeting' => 'Hello ',
            'body' => $body,
            'actionText' => 'Check it out here',
            'url' => $request->url,
            'sender' => '',
            'code' => $request->code

        ];

        Mail::to($request->users)->send(new GroupCourseInvite($details));
        return response($details, 200);
    }
    public function contactmail(Request $request)
    {


        $details = [

            'from_email' => $request->email,
            'from_name' => $request->name,
            'body' => $request->message,

        ];

        Mail::to('info@nzukoor.com')->send(new ContactMail($details));
        return response($details, 200);
    }
    public function senddiscussioninvite(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            $details = [

                'from_email' => 'info@nzukoor.com',
                'from_name' => 'Nzukoor',
                'greeting' => 'Hello',
                'body' => 'I just started a discussion, **' . $request->title . '** on Nzukoor and I’d like to hear your thoughts. ',
                'actionText' => 'Join here',
                'url' => "https://nzukoor.com/me/discussion/" . $request->id,

            ];

            Mail::to($request->users)->send(new DiscussionInvite($details));
            return response($details, 200);
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
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));


        $details = [

            'from_email' => $user->email,
            'from_name' => $user->name,
            'greeting' => 'Hello',
            'body' => 'I just started a discussion, **' . $request->title . '** on Nzukoor and I’d like to hear your thoughts. ',
            'actionText' => 'Join here',
            'url' => "https://nzukoor.com/me/discussion/" . $request->id,

        ];
        $tribe_id = Discussion::find($request->id)->tribe_id;
        Mail::to($request->users)->send(new DiscussionInvite($details));

        $url = "https://nzukoor.com/me/tribe/".$tribe_id."discussion/" . $request->id;
        $body = 'I just started a discussion, <b>' . $request->title . '</b> on Nzukoor and I’d like to hear your thoughts. <br> <a href=' . $url . '>' . $url . '</a>';


        foreach ($request->users as $key => $value) {

            $user_id = User::where('email', $value['email'])->value('id');

            if (!is_null($user_id)) {
                $user->inbox()->create([
                    'message' => $body,
                    'attachment' => '',
                    'receiver' => 'user',
                    'receiver_id' => $user_id,
                    'status' => false,

                ]);
            }
        }

        return response($details, 200);
    }
    public function guestsenddiscussioninvite(Request $request)
    {




        $details = [

            'from_email' => 'info@nzukoor.com',
            'from_name' => 'Nzukoor',
            'greeting' => 'Hello',
            'body' => 'You have been invited to join the discussion, **' . $request->title . '** on Nzukoor and We’d like to hear your thoughts. ',
            'actionText' => 'Join here',
            'url' => "https://nzukoor.com/me/discussion/" . $request->id,

        ];
        $tribe_id = Discussion::find($request->id)->tribe_id;
        Mail::to($request->users)->send(new DiscussionInvite($details));


        return response($details, 200);
    }

    public function sendeventinvite(Request $request)
    {

        if (!auth('admin')->user() && !auth('admin')->user() && !auth('admin')->user()) {

            $details = [

                'from_email' => 'info@nzukoor.com',
                'from_name' => 'Nzukoor',
                'greeting' => 'Hello',
                'body' => 'I will be attending the event, **' . $request->title . '** on Nzukoor and I think you’d like it. Join me! ',
                'actionText' => 'Join here',
                'url' => "https://nzukoor.com/explore/event/" . $request->id,

            ];

            Mail::to($request->users)->send(new EventInvite($details));
            return response($details, 200);
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
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));


        $details = [

            'from_email' => $user->email,
            'from_name' => $user->name,
            'greeting' => 'Hello',
            'body' => 'I will be attending the event, **' . $request->title . '** on Nzukoor and I think you’d like it. Join me! ',
            'actionText' => 'Join here',
            'url' => "https://nzukoor.com/event/" . $request->id,

        ];

        Mail::to($request->users)->send(new EventInvite($details));
        return response($details, 200);
    }
}
