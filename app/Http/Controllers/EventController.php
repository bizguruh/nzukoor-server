<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index()
    {
        return Event::all();
    }
    public function facilitatorgetevents()
    {
        $user = auth('facilitator')->user();
        return Event::where('organization_id', $user->organization_id)->get();
    }

    public function facilitatorgetevent($id)
    {
        return Event::where('id', $id)->first();
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
            'description' => $request->description,
            'schedule' => $request->duration,
            'facilitators' => json_encode($request->facilitators),
            'url' => $request->url,
            'start' => $request->start,
            'end' => $request->end,
            'status' => false,
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
        return $event;
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
