<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Notifications\CoursePurchase;
use Illuminate\Http\Request;

class OrderController extends Controller
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

    public function store(Request $request)
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

        $result =   $user->order()->create([
            'reference' => $request->reference,
            'message' => $request->message,
            'status' => $request->status,
            'trans' => $request->trans,
            'transaction' => $request->transaction,
            'trxref' => $request->trxref,
            'redirecturl' =>  $request->redirecturl,
            'course_id' => $request->course_id,
        ]);

        if ($request->status == 'success') {
            $user->library()->create([
                'course_id' => $request->course_id
            ]);
        }
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
        $body = "Thanks for your purchase of the course, " . strtoupper(Course::find($request->course_id)->title) . ", it has been added to your library ";
        $details = [
            'body' => $body,
            'id' => $request->course_id,
        ];
        $user->notify(new CoursePurchase($details));

        return response($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($tranx)
    {
        return  Order::where('trxref', $tranx)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
