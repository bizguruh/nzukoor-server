<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\User;
use App\Models\Report;
use App\Models\Discussion;
use App\Models\FeedComment;
use Illuminate\Http\Request;
use App\Models\DiscussionMessage;
use App\Notifications\DeletedContent;

class OrganizationReportController extends Controller
{
    public function index()
    {
        return Report::with('user')->latest()->paginate(15);
    }

    public function store()
    {
    }

    public function show(Report $report)
    {

        switch ($report->type) {
            case 'feed':
                return  Feed::find($report->type_report_id)->load('user');
                break;
            case 'feed comment':
                return  FeedComment::find($report->type_report_id)->load('user');

                break;
            case 'discussion':
                return  Discussion::find($report->type_report_id)->load('user');

                break;
            case 'discussionmessage':
                return  DiscussionMessage::find($report->type_report_id)->load('user');

                break;

            default:
                # code...
                break;
        }
    }

    public function markreport(Request $request)
    {
        $report =  Report::find($request->report_id);
        $report->status = 'okay';
        $report->save();
        return $report;
    }
    public function deletereport(Request $request)
    {
        $report =  Report::find($request->report_id);
        $report->status = 'removed';
        $report->save();
        switch ($report->type) {
            case 'feed':
                $data =  Feed::find($request->id);
                break;
            case 'feed comment':
                $data =   FeedComment::find($request->id);

                break;
            case 'discussion':
                $data =   Discussion::find($request->id);

                break;
            case 'discussionmessage':
                $data =   DiscussionMessage::find($request->id);

                break;

            default:
                # code...
                break;
        }


        $details = [
            'message'=> 'Your content as be removed due to improper conduct'
        ];
        $user = User::find($request->user_id);
        $user->notify(new DeletedContent($details));


    }
    public function destroy(Report $report)
    {
    }
}
