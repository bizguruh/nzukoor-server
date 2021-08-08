<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\MemberAssessment;
use App\Models\Library;
use Illuminate\Http\Request;

class MemberAssessmentController extends Controller
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
        $user = auth('api')->user();
        return   $user->memberassessment()->with('assessment')->latest()->get();
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
    public function addassessment(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('api')->user();
        $library = Library::where('user_id', $user->id)->get()->toArray();

        $newarr = array_map(function ($v) {
            return $v['course_id'];
        }, $library);
        $courseIds =  array_unique($newarr);

        return  $assessments = Assessment::where('course_id', $courseIds)->with('questiontemplate', 'course')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberAssessment  $memberAssessment
     * @return \Illuminate\Http\Response
     */
    public function show(MemberAssessment $memberAssessment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MemberAssessment  $memberAssessment
     * @return \Illuminate\Http\Response
     */
    public function edit(MemberAssessment $memberAssessment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MemberAssessment  $memberAssessment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MemberAssessment $memberAssessment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberAssessment  $memberAssessment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MemberAssessment $memberAssessment)
    {
        //
    }
}
