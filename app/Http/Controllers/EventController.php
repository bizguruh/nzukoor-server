<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index()
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

        return Event::where('organization_id', $user->organization_id)->with('eventattendance')->get();
    }
    public function guestindex()
    {

        return Event::with('eventattendance')->get();
    }
    public function facilitatorgetevents()
    {
        $user = auth('facilitator')->user();
        return Event::where('organization_id', $user->organization_id)->with('eventattendance')->get();
    }

    public function facilitatorgetevent($id)
    {
        return Event::where('id', $id)->with('eventattendance')->first();
    }
    public function checkEvents()
    {
        $events = Event::where('status', '!=', 'expired')->get();
        foreach ($events as $key => $event) {
            $now  = Carbon::now();
            $start = Carbon::parse($event->start);
            $end = Carbon::parse($event->end);
            $end_diff = $now->gte($end);
            if ($now->gte($start) && $now->lte($end)) {
                $event->status = 'ongoing';
            }
            if ($end_diff) {
                $event->status = 'expired';
            }
            $event->save();
        }
    }

    public function store(Request $request)
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


        return $user->event()->create([
            'type' => $request->type,
            'title' => $request->title,
            'venue' => $request->venue,
            'description' => $request->description,
            'schedule' => $request->duration,
            'facilitators' => json_encode($request->facilitators),
            'url' => $request->url,
            'start' => $request->start,
            'end' => $request->end,
            'status' => 'pending',
            'resource' => $request->resource,
            'organization_id' => $user->organization_id,
            'cover' => $request->cover,

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return $event->load('eventattendance');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Event $event)
    {
        return $request->all();
        $event->type = $request->type;
        $event->title = $request->title;
        $event->venue = $request->venue;
        $event->description  = $request->description;
        $event->schedule = $request->duration;
        $event->facilitators  = json_encode($request->facilitators);
        $event->url = $request->url;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->status = $request->status;
        $event->resource  = $request->resource;
        $event->cover = $request->cover;
        $event->save();
        return $event;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
