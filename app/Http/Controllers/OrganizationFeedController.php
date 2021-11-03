<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;

class OrganizationFeedController extends Controller
{
    public function index()
    {

        return Feed::with('user', 'comments', 'likes', 'stars')->latest()->paginate(15);
    }

    public function store()
    {
        return;
    }

    public function show(Feed $feed)
    {
        return $feed->load('user', 'comments', 'likes', 'stars');
    }

    public function destroy(Feed $feed)
    {
        $feed->delete();
        return response()->noContent();
    }
}
