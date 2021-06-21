<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\DiscussionInvite;
use App\Mail\EventInvite;
use App\Mail\GroupCourseInvite;
use App\Mail\ReferralInvite;
use App\Models\Organization;
use App\Notifications\GroupInvite;
use App\Notifications\RoleInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class MailController extends Controller
{
    public function sendroleinvite($organization, $user)
    {

        $userorg = auth('organization')->user();
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

        $details = [
            'from_email' => 'skillsguruh@gmail.com',
            'from_name' => 'SkillsGuruh',
            'greeting' => 'Hello ' . $first_name,
            'body' => 'You have been invited by ' . $organization . ' to be a ' . $user->role . ' on SkillsGuruh',
            'actionText' => 'Click to login',
            'url' => "http://skillsguruh.com/login",
            'code' => $userorg->referral_code

        ];

        $user->notify(new RoleInvite($details));
    }
    public function sendwelcome($info)
    {

        $data = [
            'name' => $info->name
        ];
        Mail::send('email.organizationwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject('Here’s Your Passport To Be More ');
            $message->from('skillsguruh@gmail.com', 'SkillsGuruh');
        });
    }

    public function sendfacilitatorwelcome($info)
    {

        $data = [
            'name' => $info->name
        ];
        Mail::send('email.facilitatorwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject(' A Home For You To Share');
            $message->from('skillsguruh@gmail.com', 'SkillsGuruh');
        });
    }

    public function sendreferral(Request $request)
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
        $organization = Organization::find($user->organization_id);
        $data = [
            'code' => $request->code,
            'name' => $user->name,
            'organization' => $organization->name,
            'from' => $user->email,
            'url' => 'https://skillsguruh.herokuapp.com/register/?referral_code=' . $request->code
        ];
        Mail::to($request->emails)->send(new ReferralInvite($data));
    }


    public function sendcourseinvite(Request $request)
    {

        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $body = 'I would you to enroll for my course, **' . $request->title . '** on SkillsGuruh';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $body = 'I would you to enroll for my course, **' . $request->title . '** on SkillsGuruh';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $body = 'Would you enroll for the course, **' . $request->title . '** on SkillsGuruh with me?';
        }
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));



        $details = [

            'from_email' => 'skillsguruh@gmail.com',
            'from_name' => 'SkillsGuruh',
            'greeting' => 'Hello ',
            'body' => $body,
            'actionText' => 'Check it out here',
            'url' => $request->url

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

        Mail::to('skillsguruh@gmail.com')->send(new ContactMail($details));
        return response($details, 200);
    }
    public function senddiscussioninvite(Request $request)
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
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));


        $details = [

            'from_email' => $user->email,
            'from_name' => $user->name,
            'greeting' => 'Hello',
            'body' => 'I just started a discussion, **' . $request->title . '** on SkillsGuruh and I’d like to hear your thoughts. ',
            'actionText' => 'Join here',
            'url' => "https://skillsguruh.herokuapp.com/learner/discussion/" . $request->id,

        ];

        Mail::to($request->users)->send(new DiscussionInvite($details));
        return response($details, 200);
    }

    public function sendeventinvite(Request $request)
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
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));


        $details = [

            'from_email' => $user->email,
            'from_name' => $user->name,
            'greeting' => 'Hello',
            'body' => 'I will be attending the event, **' . $request->title . '** on SkillsGuruh and I think you’d like it. Join me! ',
            'actionText' => 'Join here',
            'url' => "https://skillsguruh.herokuapp.com/learner/event/" . $request->id,

        ];

        Mail::to($request->users)->send(new EventInvite($details));
        return response($details, 200);
    }
}
