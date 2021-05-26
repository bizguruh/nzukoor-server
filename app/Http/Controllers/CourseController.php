<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
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
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }
        return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire')->where('organization_id', $user->organization_id)->latest()->get();
    }


    public function getcourse($id)
    {
        return Course::with('courseoutline', 'courseschedule', 'modules')->where('id', $id)->first();
    }
    public function store(Request $request)
    {

        $result = DB::transaction(function () use ($request) {
            $user = auth('admin')->user();


            $course = Course::create([
                'title' =>  $request->input('general.title'),
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
                'organization_id' => $user->organization_id,


            ]);

            foreach ($request->input('schedule') as $key => $value) {

                $schedule = $course->courseschedule()->create([
                    'day' => 'default',
                    'url' =>  $value['url'],
                    'facilitator_id' =>   $value['facilitator_id'],
                    'start_time' =>  $value['start_time'],
                    'end_time' =>  $value['end_time'],
                    'organization_id' => $user->organization_id,
                ]);
            }
            return $course->load('courseoutline', 'courseschedule', 'modules');
        });

        return $result;
    }

    public function show(Course $course)
    {
        return Course::with('curriculum')->with('module')->with('feedback')->where('id', $course->id)->first();
    }



    public function update(Request $request, Course $course)
    {


        $result = DB::transaction(function () use ($request, $course) {
            $user = auth('admin')->user();

            $course->title = $request->input('general.title');
            $course->description = $request->input('general.description');
            $course->cover  = $request->input('general.cover');
            $course->save();

            $outline = $course->courseoutline()->first();
            $outline->overview =   $request->input('outline.overview');
            $outline->additional_info =   $request->input('outline.additional_info');
            $outline->knowledge_areas =  $request->input('outline.knowledge_area');
            $outline->modules =  json_encode($request->input('outline.modules'));
            $outline->duration =   $request->input('outline.duration');
            $outline->certification =   $request->input('outline.certification');
            $outline->faqs =  json_encode($request->input('outline.faqs'));
            $outline->save();




            foreach ($request->input('schedule') as $key => $value) {

                $schedule =  CourseSchedule::firstOrNew(['id' => $value['id']]);

                $schedule->day = $value['day'];
                $schedule->facilitator_id =   $value['facilitator_id'];
                $schedule->start_time =  $value['start_time'];
                $schedule->end_time =  $value['end_time'];
                $schedule->save();
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
