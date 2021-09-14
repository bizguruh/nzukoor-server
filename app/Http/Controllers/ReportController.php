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

        switch ($request->type) {
            case 'feed':
                $type = \App\Models\Feed::class;
                break;
            case 'feedcomment':
                $type = \App\Models\FeedComment::class;
                break;
            case 'discussion':
                $type =  \App\Models\Discussion::class;
                break;
            case 'discussionmessage':
                $type = \App\Models\DiscussionMessage::class;
                break;

            default:
                # code...
                break;
        }

        return  $user->reports()->create([
            'type' => $type,
            'type_report_id' => $request->type_report_id,
            'message' => $request->message,
            'status' => 'in review'
        ]);
    }
}
