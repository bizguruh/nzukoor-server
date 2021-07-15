<?php

namespace App\Http\Controllers;

use App\Models\FacilitatorModule;
use Illuminate\Http\Request;

class FacilitatorModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('facilitator')->user();
        return  $user->facilitatormodules()->with('course')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FacilitatorModule  $facilitatorModule
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        $user = auth('facilitator')->user();
        return  $user->facilitatormodules()->where('course_id', $id)->with('course')->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FacilitatorModule  $facilitatorModule
     * @return \Illuminate\Http\Response
     */
    public function edit(FacilitatorModule $facilitatorModule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FacilitatorModule  $facilitatorModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FacilitatorModule $facilitatorModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FacilitatorModule  $facilitatorModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(FacilitatorModule $facilitatorModule)
    {
        //
    }
}
