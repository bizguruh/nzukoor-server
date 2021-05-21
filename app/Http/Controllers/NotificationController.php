<?php

namespace App\Http\Controllers;



use App\Notifications\SendNotification;
use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{

    public function sendnotifications(Request $request)
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


    public function getnotifications()
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
        return $user->notifications;

        // foreach ($user->notifications as $notification) {
        //     echo $notification->type;
        // }
    }
    public function unreadnotifications()
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
        return $user->unreadnotifications;

        foreach ($user->unreadnotifications as $notification) {
            echo $notification->type;
        }
    }

    public function markreadnotifications()
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

        $user->unreadNotifications->markAsRead();
        return  $user->notifications;
    }

    public function marksinglenotification($id)
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

        $user->unreadNotifications->where('id', $id)->markAsRead();
        return $user->notifications;
    }


    public function destroy(Notification $notification)
    {
    }
}
