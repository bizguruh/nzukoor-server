<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use Illuminate\Http\Request;
use App\Services\TribeService;
use App\Http\Resources\TribeResource;
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
        $data = Tribe::with('users')->paginate(15);

        return TribeResource::collection($data)->response()->getData(true);
    }
    public function guesttribes()
    {
        $data = Tribe::with('users')->get();
        return TribeResource::collection($data);
    }

    public function tribemembers(Tribe $tribe)
    {
        return $this->tribeservice->getmembers($tribe, $this->user);
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

        return  TribeDiscussionResource::collection($this->tribeservice->gettribediscussions($tribe))->response()->getData(true);
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
        return  $this->tribeservice->usertribe($this->user);
    }

    public function store(Request $request)
    {
        return  $this->tribeservice->create($this->user, $request);
    }

    public function show(Tribe $tribe)
    {
        return $this->tribeservice->gettribe($tribe);
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
