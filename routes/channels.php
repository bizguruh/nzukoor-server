<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('notifications', function ($user) {
    return Auth::check();
});
Broadcast::channel('transaction', function ($user) {
    return Auth::check();
});
Broadcast::channel('inbox.{toUserId}', function ($user, $toUserId) {
    return $user->id == $toUserId;
});
Broadcast::channel('addfeed', function ($user) {
    return Auth::check();
});
Broadcast::channel('addcomment', function ($user) {
    return Auth::check();
});
Broadcast::channel('adddiscussion', function ($user) {
    return Auth::check();
});

Broadcast::channel('search', function () {
    return true;
});
