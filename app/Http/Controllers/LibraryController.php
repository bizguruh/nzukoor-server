<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\SingleLibraryResource;
use App\Models\Course;
use App\Models\EnrollCount;
use App\Models\Library;
use App\Notifications\CoursePurchase;
use App\Notifications\CourseToLibrary;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class LibraryController extends Controller
{

    public function index()
    {
        $user  = auth('api')->user();
        return  LibraryResource::collection(Library::where('user_id', $user->id)->with('assessment', 'assessmentresponse')->get());
    }

    public function store(Request $request)
    {

        $user  = auth('api')->user();
        $enroll = EnrollCount::where('course_id', $request->course_id)->where('organization_id', $user->organization_id)->first();

        if (is_null($enroll)) {
            EnrollCount::create([
                'course_id' => $request->course_id,
                'organization_id' => $user->organization_id,
                'count' => 1
            ]);
        } else {
            $enroll->count = $enroll->count + 1;
            $enroll->save();
        }

        $result = $user->library()->create([
            'course_id' => $request->course_id
        ]);


        $body = "The course, " . strtoupper(Course::find($request->course_id)->title) . " has been added to your library ";
        $details = [
            'body' => $body,
            'id' => $request->course_id,
        ];
        $user->notify(new CourseToLibrary($details));
        broadcast(new NotificationSent());
        return response($result, 201);
    }

    public function show($id)
    {
        $user  = auth('api')->user();
        $library = Library::where('user_id', $user->id)->where('course_id', $id)->first();
        return new SingleLibraryResource($library);
    }

    public function updateprogress(Request $request)
    {
        $find = Library::where('course_id', $request->id)->first();

        if (is_null($find->current_module) ||  $find->current_module < $request->current_module) {

            $find->current_module = $request->current_module;
            $find->total_modules = $request->total_modules;
            $find->progress = ($request->current_module / $request->total_modules)  * 100;
        }
        $find->save();
        return $find;
    }


    public function update(Request $request, Library $library)
    {
        //
    }

    public function destroy(Library $library)
    {
        $library->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
