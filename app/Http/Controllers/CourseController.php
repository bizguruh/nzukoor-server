<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\EnrollCount;
use App\Models\FacilitatorModule;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire')->where('organization_id', $user->id)->latest()->get();
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->where('organization_id', $user->organization_id)->latest()->get();
    }

    public function show($id)
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire')->where('organization_id', $user->id)->latest()->get();
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->where('id', $id)->latest()->first();
    }
    public function guestcourses()
    {
        return Course::with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->latest()->get();
    }



    public function getcourse($id)
    {
        return Course::with('courseoutline', 'courseschedule', 'modules')->where('id', $id)->first();
    }
    public function store(Request $request)
    {

        $result = DB::transaction(function () use ($request) {
            if (auth('admin')->user()) {
                $user = auth('admin')->user();
            }
            if (auth('facilitator')->user()) {
                $user = auth('facilitator')->user();
            }
            if (auth('api')->user()) {
                $user = auth('api')->user();
            }


            $course = Course::create([
                'title' =>  $request->input('general.title'),
                'description' =>  $request->input('general.description'),
                'code' => $request->input('general.code'),
                'cover'  =>  $request->input('general.cover'),
                'type' => $request->input('general.type'),
                'amount' => $request->input('general.amount'),
                'organization_id' => $user->organization_id,
            ]);
            $outline = $course->courseoutline()->create([
                'overview' =>  $request->input('outline.overview'),
                'additional_info' =>  $request->input('outline.additional_info'),
                'knowledge_areas' => json_encode($request->input('outline.knowledge_area')),
                'modules' => json_encode($request->input('outline.modules')),
                'duration' =>  $request->input('outline.duration'),
                'certification' =>  $request->input('outline.certification'),
                'faqs' => json_encode($request->input('outline.faqs')),
                'organization_id' => $user->organization_id,


            ]);

            foreach ($request->input('schedule') as $key => $value) {

                $schedule = $course->courseschedule()->create([
                    'all' => $value['all'],
                    'day' => 'default',
                    'url' =>  $value['url'],
                    'venue' =>  $value['venue'],
                    'facilitator_id' =>   $value['facilitator_id'],
                    'start_time' =>  $value['start_time'],
                    'end_time' =>  $value['end_time'],
                    'modules' => json_encode($value['modules']),
                    'organization_id' => $user->organization_id,
                ]);

                FacilitatorModule::create([
                    'course_id' => $course->id,
                    'facilitator_id' => $value['facilitator_id'],
                    'modules' => json_encode($value['modules'])
                ]);
            }



            return $course->load('courseoutline', 'courseschedule', 'modules', 'questionnaire');
        });

        return $result;
    }

    public function mostenrolled()
    {
        $user = auth('facilitator')->user();
        $enrolled = EnrollCount::where('organization_id', $user->organization_id)->with('course')->get()->toArray();



        usort($enrolled, function ($param1, $param2) {

            return strcmp($param2['count'], $param1['count']);
        });
        return $enrolled;
    }
    public function guestmostenrolled()
    {

        $enrolled = EnrollCount::with('course')->get()->toArray();

        usort($enrolled, function ($param1, $param2) {

            return strcmp($param2['count'], $param1['count']);
        });
        return $enrolled;
    }

    public function toprated()
    {
        $user = auth('facilitator')->user();
        $enrolled = Course::where('organization_id', $user->organization_id)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->get()->toArray();



        function totalreview($arr)
        {

            $score = array_map(function ($param) {
                return $param['score'];
            }, $arr);
            return array_reduce($score, function ($a, $b) {
                return $a + $b;
            }, 0);
        }


        $courses = array_map(function ($val) {
            return [['total_review' => totalreview($val['review']) ? intval(totalreview($val['review']) / count($val['review'])) : 0], ['course' => $val]];
        }, $enrolled);

        usort($courses, function ($param1, $param2) {

            return strcmp($param2[0]['total_review'], $param1[0]['total_review']);
        });
        return $courses;
    }



    public function update(Request $request, Course $course)
    {


        $result = DB::transaction(function () use ($request, $course) {
            if (auth('admin')->user()) {
                $user = auth('admin')->user();
            }
            if (auth('facilitator')->user()) {
                $user = auth('facilitator')->user();
            }
            if (auth('api')->user()) {
                $user = auth('api')->user();
            }

            $course->title = $request->input('general.title');
            $course->type = $request->input('general.type');
            $course->amount = $request->input('general.amount');
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
                $schedule->all = $value['all'];
                $schedule->modules = json_encode($value['modules']);
                $schedule->venue = $value['venue'];
                $schedule->facilitator_id =   $value['facilitator_id'];
                $schedule->start_time =  $value['start_time'];
                $schedule->end_time =  $value['end_time'];
                $schedule->save();

                $find = FacilitatorModule::where('course_id', $course->id)->first();
                $find->modules = json_encode($value['modules']);
                $find->save();
            }
            //return $request->questionnaires;

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
