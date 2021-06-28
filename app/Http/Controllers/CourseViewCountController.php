<?php

namespace App\Http\Controllers;

use App\Models\CourseViewCount;
use Illuminate\Http\Request;

class CourseViewCountController extends Controller
{
    public function store($id)
    {


        $enroll = CourseViewCount::where('course_id', $id)->first();

        if (is_null($enroll)) {
            CourseViewCount::create([
                'course_id' => $id,
                'count' => 1
            ]);
        } else {
            $enroll->count = $enroll->count + 1;
            $enroll->save();
        }
    }
}
