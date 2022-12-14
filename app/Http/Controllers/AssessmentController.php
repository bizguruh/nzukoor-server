<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
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

        $user = auth('facilitator')->user();
        return $user->assessments()->with('questiontemplate', 'course', 'assessmentresponse')->latest()->get();
    }


    public function store(Request $request)
    {

        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user()) {
            return ('Unauthorized');
        }


        $user = auth('facilitator')->user();
        $assessment =  $user->assessments()->create([
            'type' => $request->type,
            'start' => $request->start,
            'end' => $request->end,
            'question_template_id' => $request->template['id'],
            'duration' => $request->duration,
            'tools' => json_encode($request->tools),
            'feedback' => $request->feedback,
            'course_id' => $request->course['id'],
            'status' => 'pending'
        ]);
        return response($assessment->load('questiontemplate', 'course', 'assessmentresponse'), 201);
    }


    public function show(Assessment $assessment)
    {


        return $assessment->load('questiontemplate', 'course', 'assessmentresponse');
    }


    public function update(Request $request, Assessment $assessment)
    {
        //
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
