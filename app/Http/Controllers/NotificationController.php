<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Admin;
use App\Models\Discussion;
use App\Models\DiscussionRequest;
use App\Models\Facilitator;
use App\Models\NotificationResponse;
use App\Models\User;
use App\Notifications\DiscussionReject;
use App\Notifications\SendNotification;
use App\Notifications\JoinDiscussion;
use App\Notifications\NewConnection;
use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{

    public function getnotificationresponse()
    {
        NotificationResponse::where('user');
    }
    public function sendnotifications(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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
        broadcast(new NotificationSent());
        return 'done';
    }

    public function sendnotification(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }

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
        broadcast(new NotificationSent());
        return 'Notification sent';
    }
    public function newconnection(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
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

        $receiver =  $request->type == 'user' ?  User::find($request->id) :  Facilitator::find($request->id);
        $details = [
            'from_name' => 'Nzukoor',
            'from_email' => 'nzukoor@gmail.com',
            'greeting' => 'Hello',
            'body' => $user->name . " added you has a connection",
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => "https://nzukoor.com/member/connections",
        ];

        $receiver->notify(new NewConnection($details));
        broadcast(new NotificationSent());
        return 'Notification sent';
    }


    public function joinDiscussionRequest(Request $request)
    {
        if (!auth('admin')->user() && !auth('facilitator')->user() && !auth('api')->user() && !auth('organization')->user()) {
            return ('Unauthorized');
        }
        if (auth('organization')->user()) {
            $user = auth('organization')->user();
            $sender = 'admin';
        }

        if (auth('admin')->user()) {
            $user = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $sender = "facilitator";
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $sender = 'user';
        }

        $discussion = Discussion::find($request->discussion_id);
        if ($discussion->creator == 'admin') {
            $creator = Admin::find($discussion->admin_id);
        }
        if ($discussion->creator == 'facilitator') {
            $creator = Facilitator::find($discussion->facilitator_id);
        }
        if ($discussion->creator == 'user') {
            $creator = User::find($discussion->user_id);
        }

        $details = [
            'from_name' => $user->username,
            'from_email' => $user->email,
            'greeting' => $discussion->name,
            'body' => $user->name . " has requested access to join your discussion, " . strtoupper($discussion->name),
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => "https://nzukoor.com/explore/discussion/" . $request->discussion_id,
            'id' => $request->discussion_id,
            'sender_id' => $user->id,
            'sender' => $sender

        ];

        $creator->notify(new joinDiscussion($details));

        $creator->discussionrequest()->create([
            'type_id' => $user->id,
            'type' => $sender,
            'discussion_id' => $request->discussion_id,
            'response' => 'pending',
            'body' => $user->name . " has requested to join your discussion, " . strtoupper($discussion->name),
        ]);
        return 'Notification sent';
    }

    public function discussionreject(Request $request)
    {



        $discussion = Discussion::find($request->discussion_id);
        if ($request->type == 'admin') {
            $user = Admin::find($request->type_id);
        }
        if ($request->type == 'facilitator') {
            $user = Facilitator::find($request->type_id);
        }
        if ($request->type == 'user') {
            $user = User::find($request->type_id);
        }

        $details = [
            'from_name' => $user->name,
            'from_email' => $user->email,
            'greeting' => $discussion->name,
            'body' => "Your request to join the discussion, " . strtoupper($discussion->name) . ' has been rejected',
            'thanks' => 'Thanks',
            'actionText' => 'Click to view',
            'url' => "https://nzukoor.com/explore/discussion/" . $request->discussion_id,
            'id' => $request->discussion_id
        ];

        $user->notify(new DiscussionReject($details));
        broadcast(new NotificationSent());

        if (auth('organization')->user()) {
            $creator = auth('organization')->user();
        }

        if (auth('admin')->user()) {
            $creator = auth('admin')->user();
        }
        if (auth('facilitator')->user()) {
            $creator = auth('facilitator')->user();
        }
        if (auth('api')->user()) {
            $creator = auth('api')->user();
        }



        $disrequest = DiscussionRequest::where('id', $request->id)->first();
        $disrequest->response = 'rejected';
        $disrequest->save();
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
