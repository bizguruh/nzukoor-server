<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;
use App\Http\Resources\DiscussionResource;
use App\Http\Resources\TribeDiscussionResource;

class OrganizationDiscussionController extends Controller
{
    public function index()
    {
        return TribeDiscussionResource::collection(Discussion::with('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe')->paginate(15));
    }

    public function store()
    {
    }

    public function show(Discussion $discussion)
    {

        return new DiscussionResource($discussion->load('user', 'discussionmessage', 'discussionvote', 'discussionview', 'tribe'));
    }

    public function destroy(Discussion $discussion)
    {
        $discussion->delete();
        return response()->noContent();
    }
}
