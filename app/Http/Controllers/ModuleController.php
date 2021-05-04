<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Module::all();
    }

    public function store(Request $request)
    {
        $user = auth('facilitator')->user();
        return $user->module()->create([

            'title' => $request->title,
            'description' => $request->description,
            'cover' => $request->cover,
            'content' => json_encode($request->content),
            'course_id' => $request->course_id,
            'organization_id' => $user->organization_id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        return $module;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Module $module)
    {

        $module->title  = $request->title;
        $module->description = $request->description;
        $module->cover = $request->cover;
        $module->content = json_encode($request->content);
        $module->save();
        return $module;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
