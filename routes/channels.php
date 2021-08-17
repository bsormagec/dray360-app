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

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('object-locking', function ($user) {
    return $user ? true : false;
});

Broadcast::channel('request-status-updated', function ($user) {
    return $user->isAbleTo('all-companies-view');
});

Broadcast::channel('request-status-updated-company{companyId}', function ($user, $companyId) {
    return $user->getCompanyId() == $companyId;
});
