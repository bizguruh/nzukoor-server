<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OrganizationEventController extends Controller
{
    public function index()
    {
        return Event::with('eventattendance', 'facilitator', 'tribe')->latest()->paginate(15);
    }


    public function show(Event $event)
    {
        return $event->load('eventattendance', 'facilitator', 'tribe');
    }

    public function destroy(Event $event)
    {

        $event->delete();
        return response()->noContent();
    }
}
