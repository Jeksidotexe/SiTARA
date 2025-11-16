<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id_users}', function ($user, $id_users) {
    return (int) $user->id_users === (int) $id_users;
});

Broadcast::channel('notifications.pimpinan', function ($user) {
    return $user->role === 'pimpinan';
});

Broadcast::channel('notifications.user.{userId}', function ($user, $userId) {
    return (int) $user->id_users === (int) $userId;
});

Broadcast::channel('wilayah-updates', function () {
    return true;
});
