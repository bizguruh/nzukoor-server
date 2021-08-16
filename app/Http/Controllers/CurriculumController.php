<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth('admin')->user();


        $user = auth('admin')->user();
        return Curriculum::where('organization_id', $user->organization_id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user = auth('admin')->user();

        return Curriculum::create([
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'course_id' => $request->course_id,
            'content' => json_encode($request->content)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function show(Curriculum $curriculum)
    {
        return $curriculum;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Curriculum $curriculum)
    {
        $curriculum->content = json_encode($request->content);
        $curriculum->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
