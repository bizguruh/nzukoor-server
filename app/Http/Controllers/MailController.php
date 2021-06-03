<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendroleinvite($organization, $user)
    {
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

        $data = [
            'organization' => $organization,
            'role' => $user->role,
            'username' => $first_name

        ];

        Mail::send('email.roleinvite', $data, function ($message) use ($data, $user) {
            $message->to($user->email, $user->name)->subject($data['organization'] . 'ROLE INVITATION');
            $message->from('successahon@gmail.com', 'SkillsGuruh');
        });
    }
    public function sendwelcome($info)
    {

        $data = [
            'name' => $info->name
        ];
        Mail::send('email.organizationwelcome', $data, function ($message) use ($info) {
            $message->to($info->email, $info->name)->subject('WELCOME MAIL');
            $message->from('successahon@gmail.com', 'SkillsGuruh');
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
            'organization' => $organization->name
        ];

        Mail::send('email.referralemail', $data, function ($message) use ($request, $data) {
            $message->to($request->email, 'Referral')->subject(strtoupper($data['organization']) . ' INVITE MAIL');
            $message->from('successahon@gmail.com', 'SkillsGuruh');
        });
    }
}
