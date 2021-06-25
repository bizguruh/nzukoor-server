<?php

namespace App\Http\Controllers;

use App\Models\AnsweredQuestionnaire;
use Illuminate\Http\Request;

class AnsweredQuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        return $user->answeredquestionnaire();
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

        return $user->answeredquestionnaire()->create([
            'content' => json_encode($request->content),
            'module_id' => $request->module_id,
            'course_id' => intval($request->course_id),
            'question_template_id' => $request->questionnaire_id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AnsweredQuestionnaire  $answeredQuestionnaire
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

        return $user->answeredquestionnaire()->where('course_id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AnsweredQuestionnaire  $answeredQuestionnaire
     * @return \Illuminate\Http\Response
     */
    public function edit(AnsweredQuestionnaire $answeredQuestionnaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AnsweredQuestionnaire  $answeredQuestionnaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AnsweredQuestionnaire $answeredQuestionnaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AnsweredQuestionnaire  $answeredQuestionnaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(AnsweredQuestionnaire $answeredQuestionnaire)
    {
        //
    }
}
