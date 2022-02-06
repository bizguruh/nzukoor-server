<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Admin;
use App\Models\Discussion;
use App\Models\DiscussionRequest;
use App\Models\Facilitator;
use App\Models\NotificationResponse;
use App\Models\PrivateDiscussionMember;
use App\Models\User;
use App\Notifications\DiscussionAcceptance;
use Illuminate\Http\Request;

class PrivateDiscussionMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $type = 'admin';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $type = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $type = 'user';
        }
        return $user->privatediscusion()->get();
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

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if ($request->type == 'admin') {
            $user = Admin::find($request->type_id);
        }
        if ($request->type == 'facilitator') {
            $user = Facilitator::find($request->type_id);
        }
        if ($request->type == 'user') {
            $user = User::find($request->type_id);
        }
        $discussion =   Discussion::find($request->discussion_id);
        $details = [
            'from_name' => $user->name,
            'from_email' => $user->email,
            'greeting' => $discussion->name,
            'body' => "Your request to join the discussion, " . strtoupper($discussion->name) . ' has been accepted',
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => "https://nzukoor.com/g/discussion/" . $request->discussion_id,
            'id' => $request->discussion_id


        ];


        $user->privatediscusion()->create([
            'discussion_id' => $request->discussion_id,
            'type' => $request->type
        ]);

        if (auth('organization')->user()) {
            $creator = auth('organization')->user();
        }

        if (auth('admin')->user()) {
            $creator = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $creator = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $creator = auth('api')->user();
        }


        $user->notify(new DiscussionAcceptance($details));
        broadcast(new NotificationSent());
        $disrequest = DiscussionRequest::where('id', $request->id)->first();
        $disrequest->response = 'accepted';
        $disrequest->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrivateDiscussionMember  $privateDiscussionMember
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PrivateDiscussionMember::where('discussion_id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrivateDiscussionMember  $privateDiscussionMember
     * @return \Illuminate\Http\Response
     */
    public function edit(PrivateDiscussionMember $privateDiscussionMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrivateDiscussionMember  $privateDiscussionMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrivateDiscussionMember $privateDiscussionMember)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrivateDiscussionMember  $privateDiscussionMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrivateDiscussionMember $privateDiscussionMember)
    {
        //
    }
}
