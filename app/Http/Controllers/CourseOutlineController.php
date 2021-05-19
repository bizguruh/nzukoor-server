<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOutline;
use Illuminate\Http\Request;

class CourseOutlineController extends Controller
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
        return CourseOutline::where('organization_id', $user->organization_id)->with('course')->latest()->get();
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

        CourseOutline::create([
            'overview' =>  $request->input('overview'),
            'additional_info' =>  $request->input('additional_info'),
            'knowledge_areas' =>  $request->input('knowledge_area'),
            'modules' => json_encode($request->input('modules')),
            'duration' =>  $request->input('duration'),
            'certification' =>  $request->input('certification'),
            'faqs' => json_encode($request->input('faqs')),
            'course_id' => $request->input('course_id'),
            'organization_id' => $user->organization_id
        ]);

        return Course::find($request->input('course_id'))->load('courseoutline');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseOutline  $courseOutline
     * @return \Illuminate\Http\Response
     */
    public function show(CourseOutline $courseOutline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseOutline  $courseOutline
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseOutline $courseOutline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseOutline  $courseOutline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {


        $outline = CourseOutline::find($id);


        $outline->overview =   $request->input('overview');
        $outline->additional_info =   $request->input('additional_info');
        $outline->knowledge_areas =  $request->input('knowledge_area');
        $outline->modules =  json_encode($request->input('modules'));
        $outline->duration =   $request->input('duration');
        $outline->certification =   $request->input('certification');
        $outline->faqs =  json_encode($request->input('faqs'));
        $outline->save();
        return $outline;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CourseOutline  $courseOutline
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseOutline $courseOutline)
    {
        $courseOutline->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
