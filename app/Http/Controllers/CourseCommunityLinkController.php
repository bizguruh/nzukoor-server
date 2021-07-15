<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCommunityLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseCommunityLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

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
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

        $user = auth('api')->user();
        $course =  Course::find($request->course_id);
        $code = $new_str = preg_replace("/\s+/", "-", $course->title) . '-' . $this->generateCode(2);
        $check = $user->communitylink()->where('code', $code)->first();

        while (!is_null($check)) {
            $code =  preg_replace("/\s+/", "-", $course->title) . '-' . $this->generateCode(2);
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
        $co = Course::find($request->course_id);
        $message = '<p>I enrolled for the course, ' . $co->title . ' course and I think you’d like it. Join me!</p> <p>' .  Str::limit($co->description, 50) . '...</p>';
        $url = 'https://skillsguruh.com/learner/courses/?course_id=' . $request->course_id;
        $data = $user->feeds()->create([
            'organization_id' => $user->organization_id,
            'media' => $co->cover,
            'url' => $url,
            'message' => $message
        ]);


        return  response($link, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseCommunityLink  $courseCommunityLink
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }

        $user = auth('api')->user();
        return $user->coursecommunity()->where('course_id', $id)->first();
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
