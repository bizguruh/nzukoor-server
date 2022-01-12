<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use App\Models\Discussion;
use App\Support\Collection;
use App\Models\TribeRequest;
use Illuminate\Http\Request;
use App\Services\TribeService;
use App\Http\Resources\TribeResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\TribeDiscussionResource;

class TribeController extends Controller
{

    public $user;
    public $tribeservice;


    public function __construct(TribeService $tribeservice)
    {
        $this->user = auth('api')->user();
        $this->tribeservice = $tribeservice;
    }


    public function index()
    {
        $currentPage = request()->get('page', 1);
        $data = Tribe::with('users')->paginate(15);

        $tribes = TribeResource::collection($data)->response()->getData(true);
        return Cache::tags(['tribes'])->remember('tribes' . $currentPage, 3600, function () use ($tribes) {
            return $tribes;
        });
    }
    public function guesttribes()
    {
        $data = Tribe::with('users')->inRandomOrder()->take(6)->get();
     return   $tribes = TribeResource::collection($data);
        // return Cache::tags(['guesttribes'])->remember('guesttribes', 3600, function () use ($tribes) {
        //     return $tribes;
        // });
    }

    public function tribemembers(Tribe $tribe)
    {
        $user = $this->user;
        $currentPage = request()->get('page', 1);
        $tribemembers = $tribe->users()->get();
        // ->filter(function ($a) use ($user) {
        //     return $a->id != $user->id;
        // });
        $members = (new Collection($tribemembers))->paginate(15);
        return Cache::tags(['tribemembers'])->remember('tribemembers' . $tribe->id . '-' . $currentPage, 60, function () use ($members) {
            return $members;
        });
    }

    public function suggestedtribe()
    {

        return $this->tribeservice->suggestedtribe($this->user);
    }

    public function tribefeeds(Tribe $tribe)
    {
        return $this->tribeservice->gettribefeeds($tribe);
    }

    public function tribecourses(Tribe $tribe)
    {
        return $this->tribeservice->gettribecourses($tribe);
    }

    public function tribeevents(Tribe $tribe)
    {
        return $this->tribeservice->gettribeevents($tribe);
    }
    public function mytribeevents(Tribe $tribe)
    {
        return $this->tribeservice->getmytribeevents($tribe, $this->user);
    }

    public function tribediscussions(Tribe $tribe)
    {
        $currentPage = request()->get('page', 1);
        $tribediscsussions =  TribeDiscussionResource::collection($this->tribeservice->gettribediscussions($tribe))->response()->getData(true);
        return Cache::tags(['tribediscussions'])->remember('tribediscussions' . $tribe->id . '-' . $currentPage, 60, function () use ($tribediscsussions) {
            return $tribediscsussions;
        });
    }
    public function addusertotribe(Tribe $tribe)
    {
        return $this->tribeservice->addusertotribe($tribe, $this->user);
    }
    public function createtriberequest(Tribe $tribe)
    {

        return $this->tribeservice->createtriberequest($tribe, $this->user);
    }
    public function respondtriberequest(Request $request ,TribeRequest $triberequest)
    {
        return $this->tribeservice->respondtriberequest($triberequest, $request);
    }

    public function gettribesrequest(){
        return $this->tribeservice->gettribesrequest($this->user);
    }



    public function leavetribe(Tribe $tribe)
    {
        return $this->tribeservice->leavetribe($tribe, $this->user);
    }
    public function getusertribe()
    {
        // $currentPage = request()->get('page', 1);
        return  $this->tribeservice->usertribe($this->user);

    }

    public function store(Request $request)
    {
        return  $this->tribeservice->create($this->user, $request);
    }

    public function show(Tribe $tribe)
    {


        $showtribe = $this->tribeservice->gettribe($tribe, $this->user);
        return Cache::tags(['showtribe'])->remember('showtribe' . $tribe->id, 60, function () use ($showtribe) {
            return $showtribe;
        });
    }
    public function checktribe(Tribe $tribe)
    {
        return $this->tribeservice->checktribe($tribe, $this->user);
    }
    public function checkdiscussiontribe(Discussion $discussion)
    {

        $tribe = Tribe::find($discussion->tribe_id);
        return $this->tribeservice->checktribe($tribe, $this->user);
    }


    public function update(Request $request, Tribe $tribe)
    {

        $owner = $tribe->getTribeOwner();
        if ($this->user->id !== $owner->id) return response('Unauthorised', 401);

        return $this->tribeservice->update($this->user, $request, $tribe);
    }


    public function destroy(Tribe $tribe)
    {

        $owner = $tribe->getTribeOwner();
        if ($this->user->id !== $owner->id) return response('Unauthorised', 401);

        return $this->tribeservice->remove($tribe);
    }
}
