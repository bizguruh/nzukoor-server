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
        return $user->answeredquestionnaire()->with('questiontemplate', 'course')->get();
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
        $find = $user->answeredquestionnaire()->where('course_id', $request->course_id)->where('question_template_id', $request->questionnaire_id)->where('module_id', $request->module_id)->first();
        if (is_null($find)) {
            return $user->answeredquestionnaire()->create([
                'content' => json_encode($request->content),
                'module_id' => $request->module_id,
                'course_id' => intval($request->course_id),
                'question_template_id' => $request->questionnaire_id,
                'status' => $request->status,
                'total_score' => $request->total_score,
                'your_score' => $request->your_score
            ]);
        } else {
            $find->content = json_encode($request->content);
            $find->status = $request->status;
            $find->your_score = $request->your_score;
            $find->save();
            return response($find, 200);
        }
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


    public function editresponse($id)
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

        $find = AnsweredQuestionnaire::find($id);
        $find->status = 'edit';
        $find->save();
        return $find;
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
