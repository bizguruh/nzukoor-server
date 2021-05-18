<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use Illuminate\Http\Request;

class CourseScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('admin')->user();

        $d =  CourseSchedule::get();
        $d->courseschedule();
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
     * @param  \App\Models\CourseSchedule  $courseSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(CourseSchedule $courseSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseSchedule  $courseSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseSchedule $courseSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseSchedule  $courseSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CourseSchedule $courseSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CourseSchedule  $courseSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseSchedule $courseSchedule)
    {
        //
    }
}
