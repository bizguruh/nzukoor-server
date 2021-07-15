<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
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
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
        }

        $history =   $user->loginhistory()->get();
        if (count($history) == 1) {

            $user->verification = true;
            $user->save();
        }
        return $user;
    }


    public function store()
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
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

        return   $user->loginhistory()->create([
            'record' => Carbon::now()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoginHistory  $loginHistory
     * @return \Illuminate\Http\Response
     */
    public function show(LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoginHistory  $loginHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoginHistory  $loginHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoginHistory $loginHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoginHistory  $loginHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoginHistory $loginHistory)
    {
        //
    }
}
