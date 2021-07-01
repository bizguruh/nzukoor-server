<?php

namespace App\Http\Controllers;

use App\Models\HighestEarningCourse;
use Illuminate\Http\Request;

class HighestEarningCourseController extends Controller
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
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
        }

        return HighestEarningCourse::where('organization_id', $user->organization_id)->with('course')->orderBy('revenue', 'desc')->first();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HighestEarningCourse  $highestEarningCourse
     * @return \Illuminate\Http\Response
     */
    public function show(HighestEarningCourse $highestEarningCourse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HighestEarningCourse  $highestEarningCourse
     * @return \Illuminate\Http\Response
     */
    public function edit(HighestEarningCourse $highestEarningCourse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HighestEarningCourse  $highestEarningCourse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HighestEarningCourse $highestEarningCourse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HighestEarningCourse  $highestEarningCourse
     * @return \Illuminate\Http\Response
     */
    public function destroy(HighestEarningCourse $highestEarningCourse)
    {
        //
    }
}
