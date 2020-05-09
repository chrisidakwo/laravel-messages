<?php

 namespace ChrisIdakwo\Messages\Tests\Unit;

use ChrisIdakwo\Messages\MessagesRegistrar;
use ChrisIdakwo\Messages\Models\Message;
use ChrisIdakwo\Messages\Models\Room;
use ChrisIdakwo\Messages\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoomTest extends TestCase {
    use RefreshDatabase;

    public function test_room_database_table_exists() {
        $room = Room::query();

        $this->assertCount(0, $room->get());
    }

    public function test_should_create_a_new_room() {
        $room = factory(Room::class)->create();
        $this->assertSame('Plumbing sheets adhere to National Plumbing Guidelines', $room->topic);

        $room2 = factory(Room::class)->create(['topic' => 'Sample Topic']);
        $this->assertEquals('Sample Topic', $room2->topic);
    }

    public function test_should_add_members() {
        $room = factory(Room::class)->create();

        $member = $room->members()->create([
            'member_id' => 1
        ]);

        $members = $room->addMembers([2, 3]);

        $this->assertCount(3, $room->members);

        $this->assertEquals([1, 2, 3], $room->members()->pluck('member_id')->toArray());
       
        $this->assertEquals([1, 1, 1], $room->members()->pluck('room_id')->toArray());

        $this->assertEquals([1], $room->members()->distinct('room_id')->pluck('room_id')->toArray());
    }

    public function test_should_remove_members() {
        $room = factory(Room::class)->create();

        $members = $room->addMembers([1, 2, 3]);

        // There are 3 members at this point
        $this->assertCount(3, $room->members()->get()->toArray());

        $room->removeMembers(2);

        // There are two members at this point
        $this->assertCount(2, $room->members()->get()->toArray());
    }

    public function test_should_get_user_room_membership() {
        $room = factory(Room::class)->create();

        $room2 = factory(Room::class)->create(['topic' => 'Topic 2']);
        
        $room->addMembers([1, 2, 3]);

        $roomMember = $room->getMembershipFromUser(2);

        $this->assertTrue($roomMember->exists());

        $this->assertEquals(1, $roomMember->room_id);

        $this->assertEquals(2, $roomMember->member_id);
    }

    public function test_room_should_get_all_members_name() {
        $room = factory(Room::class)->create();

        $room->addMembers([1, 2, 3]);

        $membersNames = $room->getMembersInformation();

        $this->assertIsArray($membersNames);

        $this->assertNotEmpty($membersNames);
    }

    public function test_if_room_has_member() {
        $room = factory(Room::class)->create();

        $room->addMembers([1, 3]);

        $this->assertTrue($room->hasMember(3));
    }

    public function test_should_return_rooms_for_user() {
        $room = factory(Room::class)->create();

        $room->addMembers([1, 3]);

        $rooms = Room::forUser(3)->get()->toArray();

        $this->assertNotEmpty($rooms);

        $this->assertCount(1, $rooms);

        // Create another room and add a member
        $room2 = factory(Room::class)->create(['topic' => 'Sample Topic']);
        $room2->addMembers(3);

        $rooms = Room::forUser(3)->orderByDesc('id')->get();

        $this->assertCount(2, $rooms->toArray());

        $this->assertSame('Sample Topic', $rooms->first()->topic);

        $this->assertTrue(in_array('Sample Topic', $rooms->pluck('topic')->toArray()));
    }

    public function test_should_return_all_members_user_ids() {
        $room = factory(Room::class)->create();

        $room->addMembers([1, 3, 2]);

        $ids = $room->getMembersUserIds();

        $this->assertEquals(['1', '3', '2'], $ids);

        $this->assertCount(3, $ids);

        $room2 = factory(Room::class)->create(['topic' => 'Sample Topic']);

        $ids = $room2->getMembersUserIds();

        $this->assertIsArray($ids);

        $this->assertEmpty($ids);

        $this->assertCount(0, $ids);

        // Append id
        $ids = $room2->getMembersUserIds(23);

        $this->assertSame(23, head($ids));
    }

    public function test_should_find_room_by_topic() {
        factory(Room::class)->create();
        factory(Room::class)->create(['topic' => 'Sample Topic']);
        factory(Room::class)->create(['topic' => 'Lorem ipsum dolor sit amet']);
        factory(Room::class)->create(['topic' => 'Another test topic']);


        $roomModel = MessagesRegistrar::room();

        $rooms = $roomModel::findByTopic('opic')->toArray();

        $this->assertIsArray($rooms);

        $this->assertNotEmpty($rooms);

        $this->assertGreaterThanOrEqual(1, count($rooms));


        $rooms = $roomModel::findByTopic('xyz')->toArray();

        $this->assertIsArray($rooms);

        $this->assertEmpty($rooms);

        $this->assertEquals(0, count($rooms));
    }

    public function test_return_rooms_shared_by_users() {
        $room = factory(Room::class)->create();
        $room->addMembers([1, 3, 2]);

        $room2 = factory(Room::class)->create(['topic' => 'Sample Topic']);
        $room2->addMembers([1, 2]);

        $room2 = factory(Room::class)->create(['topic' => 'Another Topic']);
        $room2->addMembers([3, 2]);

        $btw = Room::between([1, 3])->get()->toArray();
        $this->assertCount(1, $btw);

        $btw = Room::between([1, 2])->get()->toArray();
        $this->assertCount(2, $btw);
    }

    public function test_should_return_messages_for_room() {
        $room = factory(Room::class)->create();
        $room->addMembers([1, 3, 2]);

        $message1 = factory(Message::class)->create();
        $message2 = factory(Message::class)->create(['body' => 'Lorem ipsum dolor sit amet']);

        $roomMessages = $room->messages()->get();
        $this->assertCount(2, $roomMessages->toArray());

        $roomMessages = $room->messages()->orderByDesc("id")->get();
        $this->assertEquals($message2->body, $roomMessages->first()->body);
        $this->assertTrue($message1->is($roomMessages->last()));
    }

    public function test_should_return_latest_message_for_room() {
        $room = factory(Room::class)->create();
        $room->addMembers([1, 3, 2]);

        $message1 = factory(Message::class)->create();
        $message2 = factory(Message::class)->create(['body' => 'Lorem ipsum dolor sit amet']);

        $this->assertNotEmpty($room->latest_message);
    }
}