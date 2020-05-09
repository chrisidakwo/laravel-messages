<?php

use ChrisIdakwo\Messages\Models\Message;
use ChrisIdakwo\Messages\Models\Room;
use ChrisIdakwo\Messages\Models\RoomMember;
use ChrisIdakwo\Messages\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $f) {
    return [
        'name' => $f->name,
        'email' => $f->email
    ];
});


$factory->define(Room::class, function () {
    return [
        'topic' => 'Plumbing sheets adhere to National Plumbing Guidelines',
    ];
});

$factory->define(Message::class, function () {
    return [
        'sender_id' => 1,
        'room_id' => 1,
        'body' => 'A message'
    ];
});

$factory->define(RoomMember::class, function () {
    return [
        'member_id' => 1,
        'room_id' => 1
    ];
});
