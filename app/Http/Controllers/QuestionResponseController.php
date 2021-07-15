<?php

namespace App\Http\Controllers;

use App\Models\QuestionResponse;
use App\Models\QuestionTemplate;
use Illuminate\Http\Request;

class QuestionResponseController extends Controller
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
        return   $user->questionresponse()->latest()->get();
    }

    public function getresponses($id)
    {
        return QuestionTemplate::find($id)->questionresponse()->latest()->get();
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('api')->user();
        return $user->questionresponse()->create([
            'question_template_id' => 4,
            'response' => json_encode($request->response),
            'your_score' => $request->your_score,
            'total_score' => $request->total_score
        ]);
    }


    public function show(QuestionResponse $questionResponse)
    {
        //
    }


    public function update(Request $request, QuestionResponse $questionResponse)
    {
        //
    }


    public function destroy(QuestionResponse $questionResponse)
    {
        //
    }
}
