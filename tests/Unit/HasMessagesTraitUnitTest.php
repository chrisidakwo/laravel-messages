<?php

namespace ChrisIdakwo\Messages\Tests\Unit;

use ChrisIdakwo\Messages\Models\Message;
use ChrisIdakwo\Messages\Tests\TestCase;
use ChrisIdakwo\Messages\Models\Room;
use ChrisIdakwo\Messages\Models\RoomMember;
use ChrisIdakwo\Messages\Models\User;

class HasMessagesTraitUnitTest extends TestCase {

    public function test_should_return_user_messages() {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
        ]);

        $room = factory(Room::class)->create();

        $message = factory(Message::class)->create(['sender_id' => $user->id, "room_id" => $room->id]);

        $userMessages = $user->messages()->get();

        $this->assertCount(1, $userMessages->toArray());

        $this->assertInstanceOf(Message::class, $userMessages->first());
        
        // $roomMember1 = factory(RoomMember::class)->create(['member_id' => $user->id]);
        // $roomMember2 = factory(RoomMember::class)->create(['member_id' => 2]);

        // $room->members()->saveMany([$roomMember1, $roomMember2]);
    }

    public function test_should_return_room_members() {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
        ]);

        $room = factory(Room::class)->create();
        $room->addMembers([$user->id, 1, 2, 3]);

        $message = factory(Message::class)->create(['sender_id' => $user->id, "room_id" => $room->id]);

        $members = $message->roomMembers()->get();

        $this->assertCount(4, $members->toArray());

        $this->assertEquals([4, 1, 2, 3], $members->pluck('member_id')->toArray());
    }

    public function test_should_return_user_rooms() {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
        ]);

        $room1 = factory(Room::class)->create();
        $room2 = factory(Room::class)->create(['topic' => 'Lorem']);
        $room3 = factory(Room::class)->create(['topic' => 'Another topic']);
        $room4 = factory(Room::class)->create(['topic' => 'Sample room topic']);

        $user->rooms()->saveMany([$room1, $room4, $room3]);
        $userRooms = $user->rooms()->get();

        $this->assertCount(3, $userRooms->toArray());

        $this->assertContains('Sample room topic', $userRooms->pluck('topic')->toArray());
    }
}