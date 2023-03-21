<?php

use Arcanedev\LogViewer\Entities\Log;
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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });


Broadcast::channel('test-broadcast', function ($user) {
    return true;
});

Broadcast::channel('testing-channel', function($user)
{
    return true;
});

Broadcast::channel('channel', function ($user) {
    return true;
});