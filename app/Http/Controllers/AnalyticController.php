<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use App\Models\TribeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticController extends Controller
{

    public $user;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function gettotaltribes()
    {
        return $this->user->tribes()->wherePivot('is_owner',1)->count();
    }
    public function gettotaltribesthisweek()
    {
        return collect($this->user->tribes()->wherePivot('is_owner', 1)->get())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function gettotaltribesbelongtoo()
    {
        return $this->user->tribes()->wherePivot('is_owner', 0)->count();
    }
    public function gettotaltribesbelongtoothisweek()
    {
        return collect($this->user->tribes()->wherePivot('is_owner', 0)->get())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function gettotaluniquemembers()
    {
        $tribes = $this->user->tribes()->wherePivot('is_owner', 1)->pluck('tribe_id');
        $members = array();
        foreach ($tribes as  $value) {
           $user = Tribe::find($value)->users()->get()
           ->filter(function($a){
               return $a['id'] !== $this->user->id;
           })->values()->all();
           array_push($members, ...$user);
        }

        $newmembers =  array_intersect_key($members, array_unique(array_column($members, 'id')));
        return [...$newmembers];

    }
    public function gettotaldiscussions()
    {
        return $this->user->discussions()->count();
    }
    public function gettotaldiscussionsthisweek()
    {
        return $this->user->discussions()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function gettotalevents()
    {
        return $this->user->events()->count();
    }
    public function getlikes()
    {
        return $this->user->likes()->count();
    }
    public function getcomments()
    {

        return $this->user->comments()->count()  + $this->user->feedcommentreplies()->count();
    }
    public function getreplies()
    {

        return  $this->user->discussionmessagecomment()->count() + $this->user->discussionmessage()->count();
    }
    public function getposts()
    {
        return $this->user->feeds()->count();
    }
    public function getvotes()
    {
        return  $this->user->discussionvote()->count() + $this->user->discussionmessagevote()->count();
    }
    public function getconnections()
    {
        return $this->user->connections()->count();
    }


    public function getlikesthisweek()
    {
        return $this->user->likes()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function getcommentsthisweek()
    {

        return $this->user->comments()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()  + $this->user->feedcommentreplies()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function getrepliesthisweek()
    {

        return  $this->user->discussionmessagecomment()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count() + $this->user->discussionmessage()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function getpoststhisweek()
    {
        return $this->user->feeds()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function getvotesthisweek()
    {
        return  $this->user->discussionvote()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count() + $this->user->discussionmessagevote()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }
    public function getconnectionsthisweek()
    {
        return $this->user->connections()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }


    public function getnewdiscussionsthisweek()
    {
        return $this->user->discussions()->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    }

    public function getuserlocation(){
        $lagos = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['state']) === 'lagos';
        })->count();
        $ogun = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['state']) === 'ogun';
        })->count();
        $edo = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['state']) === 'edo';
        })->count();
        $abuja = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['state']) === 'abuja';
        })->count();
        $delta = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['state']) === 'delta';
        })->count();
        $other = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return !strtolower($a['state']);
        })->count();
        return [$lagos, $ogun, $edo, $abuja, $delta, $other];

    }
    public function getusersgender()
    {

        $male = collect($this->gettotaluniquemembers())->filter(function($a){
           return strtolower($a['gender']) === 'male';
       })->count();
         $female = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return strtolower($a['gender']) === 'female';
        })->count();
         $other = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return !strtolower($a['gender']) ;
        })->count();
        return [$male, $female,$other];
    }
    public function getusersage()
    {
        $group1 = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return $a['age'] > 0 &&  $a['age'] < 15;
        })->count();
        $group2 = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return $a['age'] > 14 &&  $a['age'] < 25;
        })->count();
        $group3 = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return $a['age'] > 24 &&  $a['age'] < 35;
        })->count();
        $group4 = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return $a['age'] > 34 ;
        })->count();
        $group5 = collect($this->gettotaluniquemembers())->filter(function ($a) {
            return !$a['age'];
        })->count();

        return [$group1, $group2, $group3, $group4, $group5];

    }

    public function getanalytics(){
        return  [
            'ownedtribes'=> $this->gettotaltribes(),
            'belongedtribes' => $this->gettotaltribesbelongtoo(),
            'ownedtribesthisweek' => $this->gettotaltribesthisweek(),
            'belongedtribesthisweek' => $this->gettotaltribesbelongtoothisweek(),
            'uniquemembers' => count($this->gettotaluniquemembers()),
            'discussions' => $this->gettotaldiscussions(),
            'discussionsthisweek' => $this->gettotaldiscussionsthisweek(),
            'newdiscussions' => $this->getnewdiscussionsthisweek(),
            'likes' => $this->getlikes(),
            'comments' => $this->getcomments(),
            'replies' => $this->getreplies(),
            'posts' => $this->getposts(),
            'votes' => $this->getvotes(),
            'likesthisweek' => $this->getlikesthisweek(),
            'commentsthisweek' => $this->getcommentsthisweek(),
            'repliesthisweek' => $this->getrepliesthisweek(),
            'poststhisweek' => $this->getpoststhisweek(),
            'votesthisweek' => $this->getvotesthisweek(),
            'connectionsthisweek' => $this->getconnectionsthisweek(),
            'connections' => $this->getconnections(),
            'locations' => $this->getuserlocation(),
            'gender' => $this->getusersgender(),
            'ages' => $this->getusersage(),
        ];
    }
}
