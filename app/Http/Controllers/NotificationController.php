<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Discussion;
use App\Models\Facilitator;
use App\Models\User;
use App\Notifications\SendNotification;
use App\Notifications\JoinDiscussion;
use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{

    public function sendnotifications(Request $request)
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
        }
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


        $details = [
            'greeting' => $request->greeting,
            'body' => $request->body,
            'thanks' => $request->thanks,
            'actionText' => $request->action,
            'url' => $request->url,
            'to' => $type,
            'id' => $user->id
        ];
        Notification::send($user, new FirstNotify($details));
        return 'done';
    }

    public function sendnotification(Request $request)
    {

        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
        }

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


        $details = [
            'greeting' => $request->greeting,
            'body' => $request->body,
            'thanks' => $request->thanks,
            'actionText' => $request->action,
            'url' => $request->url,
            'to' => $type,
            'id' => $user->id
        ];
        $user->notify(new SendNotification($details));
        return 'Notification sent';
    }

    public function joinDiscussionRequest(Request $request)
    {

        if (auth('organization')->user()) {
            $user = auth('organization')->user();
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

        $discussion = Discussion::find($request->discussion_id);
        if ($discussion->creator == 'admin') {
            $creator = Admin::find($discussion->admin_id);
        }
        if ($discussion->creator == 'facilitator') {
            $creator = Facilitator::find($discussion->facilitator_id);
        }
        if ($discussion->creator == 'learner') {
            $creator = User::find($discussion->user_id);
        }

        $details = [
            'from_name' => $user->name,
            'from_email' => $user->email,
            'greeting' => $discussion->name,
            'body' => "$user->name has requested access to join your discussion",
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => "http://localhost:8080/discussion/$request->discussion_id",

        ];

        $creator->notify(new joinDiscussion($details));
        return 'Notification sent';
    }


    public function getnotifications()
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
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
        return $user->notifications;

        // foreach ($user->notifications as $notification) {
        //     echo $notification->type;
        // }
    }
    public function unreadnotifications()
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
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
        return $user->unreadnotifications;

        foreach ($user->unreadnotifications as $notification) {
            echo $notification->type;
        }
    }

    public function markreadnotifications()
    {
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
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

        $user->unreadNotifications->markAsRead();
        return  $user->notifications;
    }

    public function marksinglenotification($id)
    {

        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $type = 'organization';
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

        $user->unreadNotifications->where('id', $id)->markAsRead();
        return $user->notifications;
    }


    public function destroy(Notification $notification)
    {
    }
}
