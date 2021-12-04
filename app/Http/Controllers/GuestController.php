<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscussionResource;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Facilitator;
use App\Models\Feed;
use App\Models\User;
use App\Support\Collection;
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

        $discussions = Discussion::with('admin', 'user', 'discussionmessage', 'discussionvote', 'discussionview')->latest()->get()->filter(function($a) use($interest){
            $tags =  collect($a['tags'])->map(function($b){
                return strtolower($b['value']);
            });

            return in_array(strtolower($interest), $tags->toArray());
        });





        $response = [
            'discussions' => (new Collection($discussions->values()))->paginate(20)
        ];
        return $response;
    }
}
