<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\Course;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnValue;

class CourseScheduleController extends Controller
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
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        return CourseSchedule::where('organization_id', $user->organization_id)->with('course', 'facilitator')->latest()->get();
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

        $user = auth('admin')->user();
        $course = Course::find($request->course_id);
        foreach ($request->input('schedule') as $key => $value) {


            $schedule = $course->courseschedule()->create([
                'day' =>  'monday',
                'url' =>  $value['url'],
                'value' =>  $value['value'],
                'facilitator_id' =>   $value['facilitator_id'],
                'start_time' =>  $value['start_time'],
                'end_time' =>  $value['end_time'],
                'organization_id' => $user->organization_id
            ])->load('course', 'facilitator');
        }
        return $schedule;
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
    public function destroy($id)
    {
        $schedule = CourseSchedule::find($id);
        $schedule->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
