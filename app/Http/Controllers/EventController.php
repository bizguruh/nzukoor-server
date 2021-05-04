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
        $user = auth('admin')->user();
        return $user->event()->create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'facilitators' => json_encode($request->facilitators),
            'url' => $request->url,
            'resource' => json_encode($request->resource),
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
        $event->type = $request->type;
        $event->title = $request->title;
        $event->description  = $request->description;
        $event->schedule = $request->schedule;
        $event->facilitators  = json_encode($request->facilitators);
        $event->url = $request->url;
        $event->resource  = json_encode($request->resource);
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
