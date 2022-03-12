<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Tribe;
use Illuminate\Http\Request;
use App\Events\NotificationSent;
use App\Notifications\EventReminder;
use App\Notifications\NewTribeEvent;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Notification;


class EventController extends Controller
{

    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            return Event::where('organization_id', $user->id)->with('eventattendance', 'facilitator')->latest()->get();
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        return Event::with('eventattendance', 'facilitator', 'tribe')->latest()->get();
    }
    public function guestindex()
    {

        return Event::with('eventattendance', 'facilitator', 'tribe')->latest()->get();
    }
    public function guestevent($id)
    {

        return Event::find($id)->load('eventattendance', 'facilitator', 'tribe');
    }
    public function facilitatorgetevents()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('facilitator')->user();
        return Event::with('eventattendance', 'facilitator', 'tribe')->latest()->get();
    }

    public function facilitatorgetevent($id)
    {
        return Event::where('id', $id)->with('eventattendance', 'facilitator', 'tribe')->first();
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
    public function getpendingevents(Tribe $tribe)
    {
        $events = $tribe->events()->with('eventattendance', 'tribe')->where('status', 'pending')->get();
        return EventResource::collection($events);
    }
    public function getactiveevents(Tribe $tribe)
    {
        $events =  $tribe->events()->with('eventattendance', 'tribe')->where('status', 'active')->get();
        return EventResource::collection($events);
    }
    public function getexpiredevents(Tribe $tribe)
    {
        $events =  $tribe->events()->with('eventattendance', 'tribe')->where('status', 'expired')->get();
        return EventResource::collection($events);
    }

    public function store(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }


        $event = $user->event()->create([
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
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'cover' => $request->cover,
            'tribe_id' => $request->tribe_id

        ]);
        $tribe = Tribe::find($request->tribe_id);
        $tribemembers = $tribe->users()->get()->filter(function ($a) use ($user) {
            return $a->id != $user->id;
        });
        $details = [
            'from_email' => 'info@nzukoor.com',
            'from_name' =>  $tribe->name . 'Tribe - Nzukoor',
            'greeting' => 'Hello ',
            'body' => 'New Tribe Event Alert! ' . $user->username . " just created a new event in" . $tribe->name . 'Tribe',
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => 'https://nzukoor.com/me/tribes',

        ];


        Notification::send($tribemembers, new NewTribeEvent($details));
        broadcast(new NotificationSent())->toOthers();
        return response($event->load('eventattendance', 'facilitator', 'tribe'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return new EventResource($event->load('eventattendance', 'facilitator', 'tribe'));
    }

    public function eventReminder()
    {
        $events = Event::get();
        $filtered = $events->filter(function ($event) {
            $now  = Carbon::now('Africa/Lagos');
            $start = Carbon::parse($event->start);
            $diff = $start->diffInHours($now);

            return $event->status == 'pending' && $diff <= 2;
        });


        foreach ($filtered as $key => $value) {
            $details = [

                'image' => $value['cover'],
                'title' => 'Event is starting soon ',
                'from_email' => 'info@nzukoor.com',
                'from_name' => 'Nzukoor',
                'greeting' => 'Hello',
                'body' => 'The event, **' . $value['title'] . '** will be starting in few hours. Don\'t forget to Join! ',
                'actionText' => 'Join here',
                'url' => "https://nzukoor.com/me/event/" . $value['id'],

            ];
            $emails =  $value->eventattendance()->get()->map(function ($user) {
                return User::find($user['user_id']);
            });

            Notification::send($emails, new EventReminder($details));
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Event $event)
    {
        if ($request->has('title') && $request->filled('title') && !empty($request->input('title'))) {
            $event->title = $request->title;
        }
        if ($request->has('venue') && $request->filled('venue') && !empty($request->input('venue'))) {
            $event->venue = $request->venue;
        }
        if ($request->has('description') && $request->filled('description') && !empty($request->input('description'))) {
            $event->description  = $request->description;
        }
        if ($request->has('duration') && $request->filled('duration') && !empty($request->input('duration'))) {
            $event->schedule = $request->duration;
        }
        if ($request->has('facilitators') && $request->filled('facilitators') && !empty($request->input('facilitators'))) {
            $event->facilitators  = json_encode($request->facilitators);
        }
        if ($request->has('url') && $request->filled('url') && !empty($request->input('url'))) {
            $event->url = $request->url;
        }
        if ($request->has('start') && $request->filled('start') && !empty($request->input('start'))) {
            $event->start = $request->start;
        }
        if ($request->has('type') && $request->filled('type') && !empty($request->input('type'))) {
            $event->end = $request->end;
        }
        if ($request->has('status') && $request->filled('status') && !empty($request->input('status'))) {
            $event->status = $request->status;
        }
        if ($request->has('resource') && $request->filled('resource') && !empty($request->input('resource'))) {
            $event->resource  = $request->resource;
        }

        if ($request->has('cover') && $request->filled('cover') && !empty($request->input('cover'))) {
            $event->cover = $request->cover;
        }





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
