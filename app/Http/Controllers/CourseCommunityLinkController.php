<?php

namespace App\Http\Controllers;

use App\Models\CourseCommunityLink;
use Illuminate\Http\Request;

class CourseCommunityLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        return $user->communitylink()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function generateCode()
    {

        return    mt_rand(1000, 9999);
    }


    public function store(Request $request)
    {
        $user = auth('api')->user();
        $code =  'community-' . $this->generateCode(2);
        $check = $user->communitylink()->where('code', $code)->first();

        while (!is_null($check)) {
            $code =  'community-' . $this->generateCode(2);
            $check = $user->communitylink()->where('code', $code)->first();
        }


        $link =   $user->communitylink()->create([
            'code' => $code,
            'amount' => $request->amount,
            'course_id' => $request->course_id
        ]);
        $user->coursecommunity()->create([
            'code' => $link->code,
            'course_id' => $request->course_id
        ]);

        return  response($link, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseCommunityLink  $courseCommunityLink
     * @return \Illuminate\Http\Response
     */
    public function show(CourseCommunityLink $courseCommunityLink)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseCommunityLink  $courseCommunityLink
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseCommunityLink $courseCommunityLink)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseCommunityLink  $courseCommunityLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CourseCommunityLink $courseCommunityLink)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CourseCommunityLink  $courseCommunityLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseCommunityLink $courseCommunityLink)
    {
        //
    }
}
