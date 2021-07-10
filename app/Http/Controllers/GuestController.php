<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\Facilitator;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{


    public function getInterestContent($interest)
    {



        $feeds = Feed::with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->get()->toArray();
        $courses = Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->latest()->get()->toArray();
        $discussions = Discussion::with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get()->toArray();
        $users = User::latest()->get()->toArray();
        $facilitators = Facilitator::latest()->get()->toArray();


        $myfeeds = array_filter($feeds, function ($item) use ($interest) {
            if (is_null($item['tags'])) {
                return;
            }

            if (in_array($interest,  array_map(function ($a) {
                return $a->value;
            }, json_decode($item['tags'])))) {
                return $item;
            }
        });
        $mycourses = array_filter($courses, function ($item) use ($interest) {
            if ($interest == json_decode($item['courseoutline']['knowledge_areas'])->value) {
                return $item;
            }
        });
        $mydiscussion = array_filter($discussions, function ($item) use ($interest) {
            if (is_null($item['tags'])) {
                return;
            }

            if (in_array($interest,  array_map(function ($a) {
                return $a->value;
            }, json_decode($item['tags'])))) {
                return $item;
            }
        });

        $myusers = array_filter($users, function ($item) use ($interest) {
            if (is_null($item['interests'])) {
                return;
            }

            if (in_array($interest,  json_decode($item['interests']))) {
                return $item;
            }
        });
        $myfacilitators = array_filter($facilitators, function ($item) use ($interest) {
            if (is_null($item['interests'])) {
                return;
            }

            if (in_array($interest,  json_decode($item['interests']))) {
                return $item;
            }
        });

        $response = [
            'users' => $myusers,
            'facilitators' => $myfacilitators,
            'feeds' => $myfeeds,
            'courses' => $mycourses,
            'discussions' => $mydiscussion
        ];
        return $response;
    }
}
