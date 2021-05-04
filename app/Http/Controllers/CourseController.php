<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('admin')->user();
        return Course::with('curriculum')->with('module')->with('feedback')->where('organization_id', $user->organization_id)->get();
    }

    public function facilitatorgetcourses()
    {
        $user = auth('facilitator')->user();
        return Course::with('curriculum')->with('module')->with('feedback')->where('organization_id', $user->organization_id)->get();
    }

    public function facilitatorgetcourse($id)
    {
        return Course::with('curriculum')->with('module')->with('feedback')->where('id', $id)->first();
    }

    public function usergetcourses()
    {
        $user = auth('api')->user();
        return Course::with('curriculum')->with('module')->with('feedback')->where('organization_id', $user->organization_id)->get();
    }

    public function usergetcourse($id)
    {
        return Course::with('curriculum')->with('module')->with('feedback')->where('id', $id)->first();
    }

    public function store(Request $request)
    {
        $user = auth('admin')->user();

        return $user->course()->create([
            'title' => $request->title,
            'description'  => $request->description,
            'knowledge_areas'  => $request->knowledge_areas,
            'curriculum'  => $request->curriculum,
            'modules'  => json_encode($request->modules),
            'duration'  => $request->duration,
            'certification'  => $request->certification,
            'faqs'  => json_encode($request->faqs),
            'date'  => $request->date,
            'time'  => $request->time,
            'facilitators'  => json_encode($request->facilitators),
            'cover'  => $request->cover,
        ]);
    }


    public function show(Course $course)
    {
        return Course::with('curriculum')->with('module')->with('feedback')->where('id', $course->id)->first();
    }



    public function update(Request $request, Course $course)
    {
        $course->title = $request->title;
        $course->description   = $request->description;
        $course->knowledge_areas  = $request->knowledge_areas;
        $course->curriculum  = $request->curriculum;
        $course->modules  = json_encode($request->modules);
        $course->duration  = $request->duration;
        $course->certification   = $request->certification;
        $course->faqs  = json_encode($request->faqs);
        $course->date  = $request->date;
        $course->time  = $request->time;
        $course->facilitators   = json_encode($request->facilitators);
        $course->cover  = $request->cover;
        $course->save();
        return $course;
    }


    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
