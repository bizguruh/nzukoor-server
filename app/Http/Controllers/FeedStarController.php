<?php

namespace App\Http\Controllers;

use App\Models\FeedStar;
use Illuminate\Http\Request;

class FeedStarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $type = 'admin';
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $type = 'facilitator';
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $type = 'user';
        }

        $data = $user->stars()->firstOrNew([
            'organization_id' => $user->organization_id ? $user->organization_id : 1,
            'feed_id' => $request->id,
        ]);


        $data->star = !$data->star;
        $data->save();
        return $data->load('admin', 'user', 'facilitator', 'feed');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedStar  $feedStar
     * @return \Illuminate\Http\Response
     */
    public function show(FeedStar $feedStar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedStar  $feedStar
     * @return \Illuminate\Http\Response
     */
    public function edit(FeedStar $feedStar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeedStar  $feedStar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeedStar $feedStar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedStar  $feedStar
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeedStar $feedStar)
    {
        //
    }
}
