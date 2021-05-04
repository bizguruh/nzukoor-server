<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Feedback::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user = auth('api')->user();

        return $user->feedback()->create([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'event_id' => $request->event_id,
            'course_id' => $request->course_id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        return $feedback;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Feedback $feedback)
    {
        $feedback->rating  = $request->rating;
        $feedback->feedback = $request->feedback;
        $feedback->save();
        return $feedback;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
