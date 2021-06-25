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
        $user = auth('facilitator')->user();
        return $user->assessments()->with('questiontemplate')->latest()->get();
    }


    public function store(Request $request)
    {


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
        return response($assessment->load('questiontemplate', 'course'), 201);
    }


    public function show(Assessment $assessment)
    {
        //
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
