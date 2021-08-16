<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\EnrollCount;
use App\Models\FacilitatorModule;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $ttl = 60 * 60 * 24;
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

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
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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


        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

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
                'course_code' => $request->input('general.code'),
                'cover'  =>  $request->input('general.cover'),
                'type' => $request->input('general.type'),
                'amount' => $request->input('general.amount'),
                'organization_id' => $user->organization_id ? $user->organization_id : 1,
            ]);
            $outline = $course->courseoutline()->create([
                'overview' =>  $request->input('outline.overview'),
                'additional_info' =>  $request->input('outline.additional_info'),
                'knowledge_areas' => json_encode($request->input('outline.knowledge_area')),
                'modules' => json_encode($request->input('outline.modules')),
                'duration' =>  $request->input('outline.duration'),
                'certification' =>  $request->input('outline.certification'),
                'faqs' => json_encode($request->input('outline.faqs')),
                'organization_id' => $user->organization_id ? $user->organization_id : 1,


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
                    'organization_id' => $user->organization_id ? $user->organization_id : 1,
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
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('facilitator')->user();
        $enrolled = EnrollCount::where('organization_id', $user->organization_id)->with('course')->get()->toArray();



        usort($enrolled, function ($param1, $param2) {

            return strcmp($param2['count'], $param1['count']);
        });
        return $enrolled;
    }
    public function usermostenrolled()
    {

        // if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
        //     return ('Unauthorized');
        // }
        // $user = auth('api')->user();
        $enrolled = EnrollCount::with('course')->get()->toArray();



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
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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


        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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
            $course->course_code = $request->input('general.code');
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


            CourseSchedule::where('course_id', $course->id)->delete();

            foreach ($request->input('schedule') as $key => $value) {

                $schedule = $course->courseschedule()->create([
                    'all' => $value['all'],
                    'day' => 'default',
                    'url' =>  $value['url'],
                    'venue' =>  $value['venue'],
                    'facilitator_id' =>   $value['facilitator_id'] ? $value['facilitator_id'] : 'N/A',
                    'start_time' =>  $value['start_time'],
                    'end_time' =>  $value['end_time'],
                    'modules' => json_encode($value['modules']),
                    'organization_id' => $user->organization_id ? $user->organization_id : 1,
                ]);



                if (!is_null($value['facilitator_id'])) {
                    $find = FacilitatorModule::where('course_id', $course->id)->delete();
                    FacilitatorModule::create([
                        'course_id' => $course->id,
                        'facilitator_id' => $value['facilitator_id'],
                        'modules' => json_encode($value['modules'])
                    ]);
                }
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
