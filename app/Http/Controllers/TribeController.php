<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use App\Services\Tribeservice;
use Illuminate\Http\Request;

class TribeController extends Controller
{

    public $user;
    public $tribeservice;

    public function __construct(Tribeservice $tribeservice)
    {
        $this->user = auth('api')->user();
        $this->tribeservice = $tribeservice;
    }


    public function index()
    {

        return Tribe::with('users')->paginate(15);
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

    public function tribediscussions(Tribe $tribe)
    {
        return $this->tribeservice->gettribediscussions($tribe);
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
        return $this->tribeservice($this->user, $request, $tribe);
    }


    public function destroy(Tribe $tribe)
    {
        return $this->tribeservice->remove($tribe);
    }
}
