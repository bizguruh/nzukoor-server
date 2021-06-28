<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\LearnerAssessment;
use App\Models\Library;
use Illuminate\Http\Request;

class LearnerAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        return   $user->learnerassessment()->with('assessment')->latest()->get();
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
     * @param  \App\Models\LearnerAssessment  $learnerAssessment
     * @return \Illuminate\Http\Response
     */
    public function show(LearnerAssessment $learnerAssessment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LearnerAssessment  $learnerAssessment
     * @return \Illuminate\Http\Response
     */
    public function edit(LearnerAssessment $learnerAssessment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LearnerAssessment  $learnerAssessment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LearnerAssessment $learnerAssessment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LearnerAssessment  $learnerAssessment
     * @return \Illuminate\Http\Response
     */
    public function destroy(LearnerAssessment $learnerAssessment)
    {
        //
    }
}
