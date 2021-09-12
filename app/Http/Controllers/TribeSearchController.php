<?php

namespace App\Http\Controllers;

use App\Events\SearchEvent;
use App\Models\Tribe;
use Illuminate\Http\Request;

class TribeSearchController extends Controller
{
    //
    public function search(Request $request)
    {
        $query = $request->query('query');
        $tribes = Tribe::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')->with('users')
            ->get();

        //broadcast search results with Pusher channels
        event(new SearchEvent($tribes));

        return response()->json("ok");
    }

    //fetch all products
    public function get(Request $request)
    {
        $tribes = Tribe::with('users')->get();
        return response()->json($tribes);
    }
}
