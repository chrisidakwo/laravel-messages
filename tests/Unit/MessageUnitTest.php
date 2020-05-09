<?php

 namespace ChrisIdakwo\Messages\Tests\Unit;

use ChrisIdakwo\Messages\MessagesRegistrar;
use ChrisIdakwo\Messages\Models\Message;
use ChrisIdakwo\Messages\Models\Room;
use ChrisIdakwo\Messages\Models\RoomMember;
use ChrisIdakwo\Messages\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MessageTest extends TestCase {
    use RefreshDatabase;

    public function test_message_database_table_exists() {
        $message = Message::query();

        $this->assertCount(0, $message->get());
    }

    public function test_should_return_members_of_a_members() {
        $message = factory(Message::class)->create();
        $room = factory(Room::class)->create();

        $room->messages()->saveMany([$message]);

        $member1 = factory(RoomMember::class)->create();
        $member2 = factory(RoomMember::class)->create(['member_id' => 2]);
        $member3 = factory(RoomMember::class)->create(['member_id' => 3]);

        $room->members()->saveMany([$member1, $member2, $member3]);

        $this->assertEquals(3, $message->roomMembers()->count());
        $this->assertEquals(2, $message->recipients()->count());
    }

    public function test_should_return_the_message_sender() {
        factory(Room::class)->create();
        $message = factory(Message::class)->create();
        $roomMember = factory(RoomMember::class)->create();

        $this->assertNotEmpty($message->sender->toArray());
        $this->assertEquals($roomMember->member_id, $message->sender_id);
    }
}