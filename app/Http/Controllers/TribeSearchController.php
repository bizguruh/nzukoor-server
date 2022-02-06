<?php

namespace App\Http\Controllers;

use App\Support\Collection;
use App\Models\Tribe;
use App\Events\SearchEvent;
use Illuminate\Http\Request;
use App\Http\Resources\GuestTribeResource;

class TribeSearchController extends Controller
{
    //
    public function search(Request $request)
    {
        $query = $request->query('query');
        $tribes = GuestTribeResource::collection(Tribe::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')->with('users')
            ->get());

        //broadcast search results with Pusher channels
        event(new SearchEvent($tribes));

        return response()->json("ok");
    }

    public function mobilesearch(Request $request)
    {
        $query = $request->query('query');
        return GuestTribeResource::collection(Tribe::where('name', 'like', '%' . $query . '%')
            ->with('users')
            ->paginate(15));
    }

    //fetch all tribe
    public function get()
    {
        $tribes = GuestTribeResource::collection(Tribe::with('users')->get());
        return response()->json($tribes);
    }

    public function sorttribes(Request $request)
    {
        $query = $request->query('query');

        if ($query === 'alphabet') {
            $result = Tribe::with('users')->OrderBy('name')->paginate(15);
            return GuestTribeResource::collection($result);
        }
        if ($query === 'popular') {
            $result =  Tribe::with('users')->paginate(15);
            return (new Collection(GuestTribeResource::collection($result)->sortByDesc('users')->values()->all()))->paginate(15);
        }
        if ($query === 'featured') {
            $result =  Tribe::with('users','discussions')->paginate(15);
            return (new Collection(GuestTribeResource::collection($result)->sortByDesc('discussions')->values()->all()))->paginate(15);
        }
        return [
            'success' => true,
            'data' => 'No results found'
        ];
    }
}
