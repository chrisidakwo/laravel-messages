<?php

return [

    'models' => [
        'user' => config('auth.providers.users.model'),

        'message' => ChrisIdakwo\Messages\Models\Message::class,

        'room_member' => ChrisIdakwo\Messages\Models\RoomMember::class,
        
        'room' => ChrisIdakwo\Messages\Models\Room::class,
    ],

    'model_keys' => [
        'uses_uuid' => false,
    ],

    'table_names' => [
        'messages' => 'messages',

        'room_members' => 'room_members',
        
        'rooms' => 'rooms',
    ],
];
