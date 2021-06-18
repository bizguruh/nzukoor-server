<?php

namespace App\Http\Controllers;

use App\Models\QuestionTemplate;
use Illuminate\Http\Request;

class QuestionTemplateController extends Controller
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

        return QuestionTemplate::where('organization_id', $user->organization_id)->latest()->get();
    }



    public function store(Request $request)
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

        return QuestionTemplate::create([


            'organization_id' => $user->organization_id,
            'interest' => $request->interest,
            'title' => $request->title,
            'sections' => json_encode($request->sections)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuestionTemplate  $questionTemplate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return QuestionTemplate::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuestionTemplate  $questionTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionTemplate $questionTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionTemplate  $questionTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $questionTemplate = QuestionTemplate::find($id);
        $questionTemplate->title = $request->title;
        $questionTemplate->sections = json_encode($request->sections);
        $questionTemplate->save();
        return $questionTemplate;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuestionTemplate  $questionTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        QuestionTemplate::find($id)->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}