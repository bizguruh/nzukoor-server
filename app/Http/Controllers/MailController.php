<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendroleinvite()
    {
        $data = [];

        Mail::send('email.roleinvite', $data, function ($message) use ($data) {
            $message->to('succy2010@gmail.com', 'Success Ahon')->subject('ROLE INVITATION');
            $message->from('successahon@gmail.com', 'SkillsGuruh');
        });
        echo " Email Sent. Check your inbox.";
    }
    public function sendwelcome()
    {
        $data = [];

        Mail::send('email.organizationwelcome', $data, function ($message) use ($data) {
            $message->to('succy2010@gmail.com', 'Success Ahon')->subject('WELCOME MAIL');
            $message->from('successahon@gmail.com', 'SkillsGuruh');
        });
        echo " Email Sent. Check your inbox.";
    }
}
