<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
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
        return Cache::remember('tribes' . $currentPage, 60, function () use ($tribes) {
            return $tribes;
        });
    }
    public function guesttribes()
    {
        $data = Tribe::with('users')->get();
        $tribes = TribeResource::collection($data);
        return Cache::remember('guesttribes', 60, function () use ($tribes) {
            return $tribes;
        });
    }

    public function tribemembers(Tribe $tribe)
    {
        $tribemembers = $this->tribeservice->getmembers($tribe, $this->user);
        return Cache::remember('tribemembers' . $tribe->id, 60, function () use ($tribemembers) {
            return $tribemembers;
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

        $tribediscsussions =  TribeDiscussionResource::collection($this->tribeservice->gettribediscussions($tribe))->response()->getData(true);
        return Cache::remember('tribediscussions' . $tribe->id, 60, function () use ($tribediscsussions) {
            return $tribediscsussions;
        });
    }
    public function addusertotribe(Tribe $tribe)
    {
        return $this->tribeservice->addusertotribe($tribe, $this->user);
    }


    public function leavetribe(Tribe $tribe)
    {
        return $this->tribeservice->leavetribe($tribe, $this->user);
    }
    public function getusertribe()
    {
        $currentPage = request()->get('page', 1);
        $usertribes =  $this->tribeservice->usertribe($this->user);
        return Cache::remember($this->user->id . 'usertribes' . $currentPage, 60, function () use ($usertribes) {
            return $usertribes;
        });
    }

    public function store(Request $request)
    {
        return  $this->tribeservice->create($this->user, $request);
    }

    public function show(Tribe $tribe)
    {
        $showtribe = $this->tribeservice->gettribe($tribe);
        return Cache::remember('showtribe' . $tribe->id, 60, function () use ($showtribe) {
            return $showtribe;
        });
    }
    public function checktribe(Tribe $tribe)
    {
        return $this->tribeservice->checktribe($tribe, $this->user);
    }


    public function update(Request $request, Tribe $tribe)
    {
        return $this->tribeservice->update($this->user, $request, $tribe);
    }


    public function destroy(Tribe $tribe)
    {
        return $this->tribeservice->remove($tribe);
    }
}
