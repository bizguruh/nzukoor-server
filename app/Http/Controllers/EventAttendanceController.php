<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Notifications\EventSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        return $user->eventattendance();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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


        $schedule =  $user->eventattendance()->create([
            'event_id' => $request->event_id
        ]);
        $event = Event::find($request->event_id);
        $name = trim($user->name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
        $body = 'You have just been registered to attend the event, ' . $event->title . ' which is scheduled for ' . Carbon::parse($event->start)->toDayDateTimeString();
        $details = [


            'from_email' => 'skillsguruh@gmail.com',
            'from_name' => 'SkillsGuruh',
            'greeting' => 'Hello ' . $first_name,
            'body' => $body,



        ];

        $user->notify(new EventSchedule($details));
        broadcast(new NotificationSent());
        return response($schedule, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventAttendance  $eventAttendance
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EventAttendance::where('event_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventAttendance  $eventAttendance
     * @return \Illuminate\Http\Response
     */
    public function edit(EventAttendance $eventAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EventAttendance  $eventAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventAttendance $eventAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventAttendance  $eventAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventAttendance $eventAttendance)
    {
        //
    }
}
