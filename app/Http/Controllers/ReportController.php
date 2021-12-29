<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

        $user = auth('api')->user();


        $check = $user->reports()->where('type', $request->type)->where('type_report_id', $request->type_report_id)->first();
        if (is_null($check)) {
            return  $user->reports()->create([
                'type' =>  $request->type,
                'type_report_id' => $request->type_report_id,
                'message' => $request->message,
                'status' => 'in review'
            ]);
        } else {
            return response()->json('reported');
        }
    }
}
