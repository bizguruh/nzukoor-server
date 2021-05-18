<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return Course::with('courseoutline', 'courseschedule')->where('organization_id', $user->organization_id)->latest()->get();
    }



    public function store(Request $request)
    {

        $result = DB::transaction(function () use ($request) {
            $user = auth('admin')->user();


            $course = Course::create([
                'title' =>  $request->input('general.name'),
                'description' =>  $request->input('general.description'),
                'code' => $request->input('general.code'),
                'cover'  =>  $request->input('general.cover'),
                'organization_id' => $user->organization_id,
            ]);
            $outline = $course->courseoutline()->create([
                'overview' =>  $request->input('outline.overview'),
                'additional_info' =>  $request->input('outline.additional_info'),
                'knowledge_areas' =>  $request->input('outline.knowledge_area'),
                'modules' => json_encode($request->input('outline.modules')),
                'duration' =>  $request->input('outline.duration'),
                'certification' =>  $request->input('outline.certification'),
                'faqs' => json_encode($request->input('outline.faqs')),


            ]);
            foreach ($request->input('schedule') as $key => $value) {
                return  $value['facilitator_id'];
                $schedule = $course->courseschedule()->create([
                    'day' =>  $value['day'],
                    'facilitator_id' =>   $value['facilitator_id'],

                    'start_time' =>  $value['start_time'],
                    'end_time' =>  $value['end_time'],
                ]);
            }
            return $course->load('courseoutline', 'courseschedule');
        });

        return $result;
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
        $result = DB::transaction(function () use ($request) {
            $user = auth('admin')->user();


          $course->title =  $request->input('general.name')
                $course->descriotion = $request->input('general.description');

                $course->cover = $request->input('general.cover');



                $course->cover =   $request->input('outline.overview');
            $course->cover =   $request->input('outline.additional_info');
            $course->cover =  $request->input('outline.knowledge_area');
            $course->cover =  json_encode($request->input('outline.modules'));
            $course->cover =   $request->input('outline.duration');
            $course->cover =   $request->input('outline.certification');
            $course->cover =  json_encode($request->input('outline.faqs'));



            foreach ($request->input('schedule') as $key => $value) {
                return  $value['facilitator_id'];

                    $course->cover =   $value['day'];
                $course->cover =    $value['facilitator_id'];

                $course->cover =  $value['start_time'];
                $course->cover =  $value['end_time'];

            }
            return $course->load('courseoutline', 'courseschedule');
        });

        return $result;
    }


    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
