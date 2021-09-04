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

    public function getmembers()
    {
        $users = User::get()->toArray();

        $sorted = collect($users)->sortBy(function ($a) {
            return $a['created_at'];
        });
        return $sorted->values()->all();
    }

    public function getInterestContent($interest)
    {



        $feeds = Feed::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest()->get()->toArray();
        $courses = Course::where('tribe_id', null)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->latest()->get()->toArray();
        $discussions = Discussion::where('tribe_id', null)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get()->toArray();



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


        $response = [

            'feeds' => $myfeeds,
            'courses' => $mycourses,
            'discussions' => $mydiscussion
        ];
        return $response;
    }
}
