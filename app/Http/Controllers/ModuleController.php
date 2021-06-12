<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
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
        return Module::where('organization_id', $user->organization_id)->with('course', 'questionnaire')->latest()->get();
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



            $resource = $user->module()->create([

                'module' => $request->module,
                'cover_image' => $request->cover_image,
                'modules' => json_encode($request->modules),
                'course_id' => $request->course_id,
                'organization_id' => $user->organization_id
            ]);

            if (count($request->questionnaires)) {
                foreach ($request->questionnaires as $key => $value) {
                    Questionnaire::create([
                        'course_id' => $request->course_id,
                        'module_id' =>  $resource->id,
                        'organization_id' => $user->organization_id,
                        'module' => $request->module,
                        'title' => $value['title'],
                        'content' => json_encode($value['sections'])
                    ]);
                }
            }

            if (count($request->templates)) {
                foreach ($request->templates as $key => $value) {
                    Questionnaire::create([
                        'course_id' => $request->course_id,
                        'module_id' =>  $resource->id,
                        'organization_id' => $user->organization_id,
                        'module' => $request->module,
                        'title' => $value['title'],
                        'type' => $request->type,
                        'content' => $value['sections']
                    ]);
                }
            }


            return $resource;
        });

        return response($result->load('questionnaire'), 201);
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

        $module->cover_image = $request->cover_image;
        $module->modules = json_encode($request->modules);
        $module->save();
        return $module->load('course');
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
