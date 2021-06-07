<?php

namespace App\Http\Controllers;

use App\Models\CourseCommunity;
use Illuminate\Http\Request;

class CourseCommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth('api')->user();
        return  $user->coursecommunity()->with('course')->get();
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

        return   $user->coursecommunity()->create([

            'code' => $request->code
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseCommunity  $courseCommunity
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth('api')->user();
        return $user->coursecommunity()->where('course_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseCommunity  $courseCommunity
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseCommunity $courseCommunity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseCommunity  $courseCommunity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CourseCommunity $courseCommunity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CourseCommunity  $courseCommunity
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseCommunity $courseCommunity)
    {
        //
    }
}
