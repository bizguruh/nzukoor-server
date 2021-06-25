<?php

namespace App\Http\Controllers;

use App\Models\AssessmentResponse;
use Illuminate\Http\Request;

class AssessmentResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        return   $user->assessmentresponse()->latest()->get();
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
        $user = auth('api')->user();

        return $user->assessmentresponse()->create([

            'assessment_id' => $request->assessment_id,
            'response' => json_encode($request->response),
            'your_score' => $request->your_score,
            'total_score' => $request->total_score
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssessmentResponse  $assessmentResponse
     * @return \Illuminate\Http\Response
     */
    public function show(AssessmentResponse $assessmentResponse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AssessmentResponse  $assessmentResponse
     * @return \Illuminate\Http\Response
     */
    public function edit(AssessmentResponse $assessmentResponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssessmentResponse  $assessmentResponse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssessmentResponse $assessmentResponse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssessmentResponse  $assessmentResponse
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssessmentResponse $assessmentResponse)
    {
        //
    }
}
